import type { components, operations } from "./schema";

type Schemas = components["schemas"];

export type Slot = Schemas["Slot"];
export type Host = Schemas["Host"];
export type Invitee = Schemas["Invitee"];
export type Booking = Schemas["Booking"];
export type BookingRequest = Schemas["BookingRequest"];
export type BookingState = Schemas["BookingState"];
export type SlotStatus = Schemas["SlotStatus"];
export type ErrorEnvelope = Schemas["ErrorEnvelope"];

const baseUrl = (import.meta.env.VITE_API_BASE_URL ?? "").replace(/\/$/, "");

export class ApiError extends Error {
    constructor(
        public status: number,
        public body: ErrorEnvelope | unknown,
        message?: string,
    ) {
        super(message ?? `API request failed with status ${status}`);
        this.name = "ApiError";
    }
}

async function request<T>(
    path: string,
    init: RequestInit,
): Promise<T> {
    const res = await fetch(`${baseUrl}${path}`, {
        ...init,
        headers: {
            Accept: "application/json",
            ...(init.body ? { "Content-Type": "application/json" } : {}),
            ...init.headers,
        },
    });

    const isJson = res.headers.get("content-type")?.includes("application/json");
    const body = isJson ? await res.json() : await res.text();

    if (!res.ok) {
        throw new ApiError(res.status, body);
    }

    return body as T;
}

export function listAvailability(
    params: operations["listAvailability"]["parameters"]["query"],
): Promise<Slot[]> {
    const search = new URLSearchParams({
        from: params.from,
        to: params.to,
    });
    return request<Slot[]>(`/availability?${search.toString()}`, { method: "GET" });
}

export function createBooking(
    body: BookingRequest,
): Promise<Booking> {
    return request<Booking>(`/bookings`, {
        method: "POST",
        body: JSON.stringify(body),
    });
}

export function getBooking(
    id: operations["getBooking"]["parameters"]["path"]["id"],
): Promise<Booking> {
    return request<Booking>(`/bookings/${encodeURIComponent(id)}`, { method: "GET" });
}
