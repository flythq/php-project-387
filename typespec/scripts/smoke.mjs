#!/usr/bin/env node
import { readFileSync } from "node:fs";
import { fileURLToPath } from "node:url";
import { dirname, join } from "node:path";
import yaml from "js-yaml";

const here = dirname(fileURLToPath(import.meta.url));
const specPath = join(here, "..", "..", "api", "openapi", "openapi.yaml");

let failures = 0;
const check = (cond, msg) => {
    if (!cond) {
        console.error(`  FAIL: ${msg}`);
        failures++;
    } else {
        console.log(`  ok:   ${msg}`);
    }
};

let doc;
try {
    doc = yaml.load(readFileSync(specPath, "utf8"));
} catch (e) {
    console.error(`Cannot load ${specPath}: ${e.message}`);
    process.exit(1);
}

console.log(`Smoke-testing ${specPath}\n`);

check(doc.openapi === "3.1.0", `openapi version is 3.1.0 (got ${doc.openapi})`);

const paths = doc.paths || {};
const expectedPaths = ["/availability", "/bookings", "/bookings/{id}"];
for (const p of expectedPaths) {
    check(p in paths, `path ${p} present`);
}

const schemas = doc.components?.schemas || {};
const expectedSchemas = ["Host", "Invitee", "Slot", "Booking", "BookingRequest", "ErrorEnvelope"];
for (const s of expectedSchemas) {
    check(s in schemas, `schema ${s} present`);
}

const createBookingResponses = paths["/bookings"]?.post?.responses || {};
check("409" in createBookingResponses, "POST /bookings returns 409 Conflict on booked slot");

console.log("");
if (failures > 0) {
    console.error(`SMOKE FAILED: ${failures} check(s) failed`);
    process.exit(1);
}
console.log("SMOKE OK");
