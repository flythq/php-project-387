<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

#[Signature('spec:generate-server')]
#[Description('Generate server-side artifacts (DTO, enums, Form Requests, controllers, routes) from api/openapi/openapi.yaml')]
class SpecGenerateServer extends Command
{
    private const HEADER = '// Generated from api/openapi/openapi.yaml — do not edit by hand.';

    private const SPEC_PATH = 'api/openapi/openapi.yaml';

    private array $schemas = [];

    private array $enums = [];

    private array $objects = [];

    public function handle(): int
    {
        $specPath = base_path(self::SPEC_PATH);

        if (! file_exists($specPath)) {
            $this->error("Spec not found at {$specPath}");

            return self::FAILURE;
        }

        $doc = Yaml::parseFile($specPath);

        $this->schemas = $doc['components']['schemas'] ?? [];
        $paths = $doc['paths'] ?? [];

        $this->partitionSchemas();

        ksort($paths);

        $dtoCount = $this->generateDtos();
        $enumCount = $this->generateEnums();
        $requestCount = $this->generateFormRequests($paths);
        $controllerCount = $this->generateControllers($paths);
        $this->generateRoutes($paths);

        $this->info("Generated {$dtoCount} DTO(s), {$enumCount} enum(s), {$requestCount} form request(s), {$controllerCount} controller(s), routes/api.php");

        return self::SUCCESS;
    }

    private function partitionSchemas(): void
    {
        $this->enums = [];
        $this->objects = [];

        foreach ($this->schemas as $name => $schema) {
            $type = $schema['type'] ?? null;

            if ($type === 'string' && isset($schema['enum'])) {
                $this->enums[$name] = $schema;
            } elseif ($type === 'object' && isset($schema['properties'])) {
                $this->objects[$name] = $schema;
            }
        }

        ksort($this->enums);
        ksort($this->objects);
    }

    private function refName(string $ref): string
    {
        return basename($ref);
    }

    private function isEnumRef(array $propSchema): bool
    {
        if (! isset($propSchema['$ref'])) {
            return false;
        }

        $name = $this->refName($propSchema['$ref']);

        return isset($this->enums[$name]);
    }

    private function isObjectRef(array $propSchema): bool
    {
        if (! isset($propSchema['$ref'])) {
            return false;
        }

        $name = $this->refName($propSchema['$ref']);

        return isset($this->objects[$name]);
    }

    private function phpType(array $propSchema): string
    {
        if (isset($propSchema['$ref'])) {
            $name = $this->refName($propSchema['$ref']);

            if (isset($this->enums[$name]) || isset($this->objects[$name])) {
                return $name;
            }

            return $this->phpType($this->schemas[$name] ?? []);
        }

        return match ($propSchema['type'] ?? 'string') {
            'string' => 'string',
            'integer' => 'int',
            'number' => $this->numberType($propSchema),
            'boolean' => 'bool',
            'array' => 'array',
            'object' => 'array',
            default => 'mixed',
        };
    }

    private function numberType(array $schema): string
    {
        if (isset($schema['enum'])) {
            foreach ($schema['enum'] as $value) {
                if (! is_int($value)) {
                    return 'float';
                }
            }

            return 'int';
        }

        return 'int';
    }

    private function generateDtos(): int
    {
        File::ensureDirectoryExists(app_path('Dto'));

        foreach ($this->objects as $name => $schema) {
            $this->writeDto($name, $schema);
        }

        return count($this->objects);
    }

    private function writeDto(string $name, array $schema): void
    {
        $properties = $schema['properties'] ?? [];
        $required = $schema['required'] ?? [];

        $enumImports = [];
        $constructorParams = [];
        $fromArrayArgs = [];
        $toArrayEntries = [];

        foreach ($properties as $propName => $propSchema) {
            $isRequired = in_array($propName, $required);
            $nullable = $isRequired ? '' : '?';
            $default = $isRequired ? '' : ' = null';
            $phpType = $this->phpType($propSchema);

            if (isset($propSchema['$ref']) && isset($this->enums[$this->refName($propSchema['$ref'])])) {
                $enumImports[$this->refName($propSchema['$ref'])] = true;
            }

            $constructorParams[] = "        public readonly {$nullable}{$phpType} \${$propName}{$default},";

            $fromArrayArgs[] = $this->fromArrayArg($propName, $propSchema, $isRequired);
            $toArrayEntries[] = $this->toArrayEntry($propName, $propSchema);
        }

        $constructorBlock = implode("\n", $constructorParams);
        $fromArrayBlock = implode("\n", $fromArrayArgs);
        $toArrayBlock = implode("\n", $toArrayEntries);

        $useBlock = '';

        if (! empty($enumImports)) {
            ksort($enumImports);
            $useLines = [];

            foreach (array_keys($enumImports) as $enumName) {
                $useLines[] = "use App\\Dto\\Enums\\{$enumName};";
            }

            $useBlock = implode("\n", $useLines)."\n\n";
        }

        $content = <<<PHP
<?php

namespace App\Dto;

{$useBlock}class {$name}
{
    public function __construct(
{$constructorBlock}
    ) {}

    public static function fromArray(array \$data): self
    {
        return new self(
{$fromArrayBlock}
        );
    }

    public function toArray(): array
    {
        return [
{$toArrayBlock}
        ];
    }
}

PHP;

        File::put(app_path("Dto/{$name}.php"), $content);
    }

    private function fromArrayArg(string $propName, array $propSchema, bool $isRequired): string
    {
        if (isset($propSchema['$ref'])) {
            $refName = $this->refName($propSchema['$ref']);

            if (isset($this->enums[$refName])) {
                if ($isRequired) {
                    return "            {$propName}: {$refName}::from(\$data['{$propName}']),";
                }

                return "            {$propName}: array_key_exists('{$propName}', \$data) ? {$refName}::from(\$data['{$propName}']) : null,";
            }

            if (isset($this->objects[$refName])) {
                if ($isRequired) {
                    return "            {$propName}: {$refName}::fromArray(\$data['{$propName}']),";
                }

                return "            {$propName}: array_key_exists('{$propName}', \$data) ? {$refName}::fromArray(\$data['{$propName}']) : null,";
            }
        }

        if ($isRequired) {
            return "            {$propName}: \$data['{$propName}'],";
        }

        return "            {$propName}: \$data['{$propName}'] ?? null,";
    }

    private function toArrayEntry(string $propName, array $propSchema): string
    {
        if (isset($propSchema['$ref'])) {
            $refName = $this->refName($propSchema['$ref']);

            if (isset($this->enums[$refName])) {
                return "            '{$propName}' => \$this->{$propName}->value,";
            }

            if (isset($this->objects[$refName])) {
                return "            '{$propName}' => \$this->{$propName}->toArray(),";
            }
        }

        return "            '{$propName}' => \$this->{$propName},";
    }

    private function generateEnums(): int
    {
        if (empty($this->enums)) {
            return 0;
        }

        File::ensureDirectoryExists(app_path('Dto/Enums'));

        foreach ($this->enums as $name => $schema) {
            $this->writeEnum($name, $schema);
        }

        return count($this->enums);
    }

    private function writeEnum(string $name, array $schema): void
    {
        $values = $schema['enum'] ?? [];
        $cases = [];

        foreach ($values as $value) {
            $caseName = ucfirst($value);
            $cases[] = "    case {$caseName} = '{$value}';";
        }

        $casesBlock = implode("\n", $cases);

        $content = <<<PHP
<?php

namespace App\Dto\Enums;

enum {$name}: string
{
{$casesBlock}
}

PHP;

        File::put(app_path("Dto/Enums/{$name}.php"), $content);
    }

    private function validationRule(array $propSchema, string $propName): string
    {
        if (isset($propSchema['$ref'])) {
            $refName = $this->refName($propSchema['$ref']);

            if (isset($this->enums[$refName])) {
                $values = $this->enums[$refName]['enum'] ?? [];

                return 'string|in:'.implode(',', $values);
            }

            if (isset($this->objects[$refName])) {
                return 'array';
            }

            return $this->validationRule($this->schemas[$refName] ?? [], $propName);
        }

        $type = $propSchema['type'] ?? 'string';
        $format = $propSchema['format'] ?? null;

        $rule = match ($type) {
            'string' => match ($format) {
                'uuid' => 'uuid',
                'date-time' => 'date',
                'date' => 'date',
                'email' => 'email',
                'uri', 'url' => 'url',
                default => $propName === 'email' ? 'email' : 'string',
            },
            'integer' => 'integer',
            'number' => 'numeric',
            'boolean' => 'boolean',
            'array' => 'array',
            'object' => 'array',
            default => 'string',
        };

        return $rule;
    }

    private function generateFormRequests(array $paths): int
    {
        File::ensureDirectoryExists(app_path('Http/Requests/Api'));

        $count = 0;

        foreach ($paths as $path => $methods) {
            foreach ($methods as $httpMethod => $op) {
                $httpMethod = strtolower($httpMethod);

                if (! in_array($httpMethod, ['get', 'post', 'put', 'patch', 'delete'])) {
                    continue;
                }

                $operationId = $op['operationId'] ?? null;

                if ($operationId === null) {
                    continue;
                }

                $className = ucfirst($operationId).'Request';

                $rules = [];
                $pathParams = [];

                foreach ($op['parameters'] ?? [] as $param) {
                    $paramName = $param['name'];
                    $in = $param['in'];
                    $required = $param['required'] ?? false;
                    $paramSchema = $param['schema'] ?? [];

                    if (isset($paramSchema['$ref'])) {
                        $paramSchema = $this->schemas[$this->refName($paramSchema['$ref'])] ?? [];
                    }

                    $rule = $this->validationRule($paramSchema, $paramName);
                    $rules[$paramName] = ($required ? 'required|' : '').$rule;

                    if ($in === 'path') {
                        $pathParams[] = $paramName;
                    }
                }

                $bodySchema = $op['requestBody']['content']['application/json']['schema'] ?? null;

                if ($bodySchema !== null) {
                    if (isset($bodySchema['$ref'])) {
                        $bodySchema = $this->schemas[$this->refName($bodySchema['$ref'])] ?? [];
                    }

                    $bodyRules = $this->bodyRules($bodySchema);

                    foreach ($bodyRules as $key => $rule) {
                        $rules[$key] = $rule;
                    }
                }

                $this->writeFormRequest($className, $rules, $pathParams);
                $count++;
            }
        }

        return $count;
    }

    private function bodyRules(array $bodySchema): array
    {
        $rules = [];
        $properties = $bodySchema['properties'] ?? [];
        $required = $bodySchema['required'] ?? [];

        foreach ($properties as $propName => $propSchema) {
            $isRequired = in_array($propName, $required);

            if (isset($propSchema['$ref']) && $this->isObjectRef($propSchema)) {
                $refName = $this->refName($propSchema['$ref']);
                $subSchema = $this->objects[$refName];
                $subRules = $this->bodyRules($subSchema);

                foreach ($subRules as $subKey => $subRule) {
                    $rules["{$propName}.{$subKey}"] = $subRule;
                }

                continue;
            }

            $rule = $this->validationRule($propSchema, $propName);
            $rules[$propName] = ($isRequired ? 'required|' : '').$rule;
        }

        return $rules;
    }

    private function writeFormRequest(string $className, array $rules, array $pathParams): void
    {
        ksort($rules);

        $rulesEntries = [];

        foreach ($rules as $key => $rule) {
            $rulesEntries[] = "            '{$key}' => '{$rule}',";
        }

        $rulesBlock = implode("\n", $rulesEntries);

        $prepareMethod = '';

        if (! empty($pathParams)) {
            $mergeEntries = [];

            foreach ($pathParams as $param) {
                $mergeEntries[] = "            '{$param}' => \$this->route('{$param}'),";
            }

            $mergeBlock = implode("\n", $mergeEntries);

            $prepareMethod = <<<PHP

    protected function prepareForValidation(): void
    {
        \$this->merge([
{$mergeBlock}
        ]);
    }

PHP;
        }

        $content = <<<PHP
<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class {$className} extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
{$rulesBlock}
        ];
    }
{$prepareMethod}}

PHP;

        File::put(app_path("Http/Requests/Api/{$className}.php"), $content);
    }

    private function generateControllers(array $paths): int
    {
        File::ensureDirectoryExists(app_path('Http/Controllers/Api'));

        $groups = [];

        foreach ($paths as $path => $methods) {
            foreach ($methods as $httpMethod => $op) {
                $httpMethod = strtolower($httpMethod);

                if (! in_array($httpMethod, ['get', 'post', 'put', 'patch', 'delete'])) {
                    continue;
                }

                $resource = $this->resourceName($path);
                $method = $this->controllerMethod($httpMethod, $path);

                $groups[$resource][$method] = [
                    'path' => $path,
                    'op' => $op,
                    'httpMethod' => $httpMethod,
                ];
            }
        }

        ksort($groups);

        foreach ($groups as $resource => $methods) {
            $this->writeController($resource, $methods);
        }

        return count($groups);
    }

    private function resourceName(string $path): string
    {
        $segments = explode('/', trim($path, '/'));

        return ucfirst($segments[0] ?? '');
    }

    private function controllerMethod(string $httpMethod, string $path): string
    {
        $hasPathParam = (bool) preg_match('/\{[^}]+\}/', $path);

        return match ($httpMethod) {
            'get' => $hasPathParam ? 'show' : 'index',
            'post' => 'store',
            'put', 'patch' => 'update',
            'delete' => 'destroy',
        };
    }

    private function writeController(string $resource, array $methods): void
    {
        $className = "{$resource}Controller";

        $imports = ["use App\Http\Controllers\Controller;"];
        $methodStrings = [];

        foreach ($methods as $methodName => $data) {
            $operationId = $data['op']['operationId'] ?? null;

            if ($operationId === null) {
                continue;
            }

            $requestClass = ucfirst($operationId).'Request';
            $imports[] = "use App\Http\Requests\Api\\{$requestClass};";

            $body = $this->methodBody($data);
            $hasPathParam = (bool) preg_match('/\{([^}]+)\}/', $data['path']);
            $pathParam = null;

            if ($hasPathParam) {
                preg_match('/\{([^}]+)\}/', $data['path'], $matches);
                $pathParam = $matches[1];
            }

            if ($pathParam !== null) {
                $methodStrings[] = <<<PHP
    public function {$methodName}({$requestClass} \$request, string \${$pathParam})
    {
        // TODO: implement — generated stub.
        {$body}
    }
PHP;
            } else {
                $methodStrings[] = <<<PHP
    public function {$methodName}({$requestClass} \$request)
    {
        // TODO: implement — generated stub.
        {$body}
    }
PHP;
            }
        }

        $importsBlock = implode("\n", array_unique($imports));
        $methodsBlock = implode("\n\n", $methodStrings);

        $content = <<<PHP
<?php

namespace App\Http\Controllers\Api;

{$importsBlock}

class {$className} extends Controller
{
{$methodsBlock}
}

PHP;

        File::put(app_path("Http/Controllers/Api/{$className}.php"), $content);
    }

    private function methodBody(array $data): string
    {
        $httpMethod = $data['httpMethod'];
        $responses = $data['op']['responses'] ?? [];
        $hasPathParam = (bool) preg_match('/\{[^}]+\}/', $data['path']);

        if ($httpMethod === 'get' && $hasPathParam) {
            return "return response()->json(['code' => 'not_found', 'message' => 'Resource not found'], 404);";
        }

        if ($httpMethod === 'get') {
            return 'return response()->json([]);';
        }

        if ($httpMethod === 'post') {
            $code = $this->successCode($responses) ?? 201;

            return "return response()->json(new \\stdClass, {$code});";
        }

        if ($httpMethod === 'delete') {
            return 'return response()->noContent();';
        }

        $code = $this->successCode($responses) ?? 200;

        return "return response()->json(new \\stdClass, {$code});";
    }

    private function successCode(array $responses): ?int
    {
        foreach ($responses as $code => $resp) {
            if (str_starts_with((string) $code, '2')) {
                return (int) $code;
            }
        }

        return null;
    }

    private function generateRoutes(array $paths): int
    {
        $imports = ['use Illuminate\Support\Facades\Route;'];
        $routeLines = [];

        foreach ($paths as $path => $methods) {
            foreach ($methods as $httpMethod => $op) {
                $httpMethod = strtolower($httpMethod);

                if (! in_array($httpMethod, ['get', 'post', 'put', 'patch', 'delete'])) {
                    continue;
                }

                $resource = $this->resourceName($path);
                $method = $this->controllerMethod($httpMethod, $path);
                $controller = "{$resource}Controller";

                $imports[] = "use App\\Http\\Controllers\\Api\\{$controller};";
                $routeLines[] = "    Route::{$httpMethod}('{$path}', [{$controller}::class, '{$method}']);";
            }
        }

        $imports = array_unique($imports);
        sort($imports);
        $importsBlock = implode("\n", $imports);
        $routesBlock = implode("\n", $routeLines);

        $content = <<<PHP
<?php

{$importsBlock}

Route::prefix('/')->group(function (): void {
{$routesBlock}
});

PHP;

        File::put(base_path('routes/api.php'), $content);

        return count($routeLines);
    }
}
