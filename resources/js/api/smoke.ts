import {
    createBooking,
    getBooking,
    listAvailability,
    type Booking,
    type BookingRequest,
    type Slot,
} from "./index";

const from = "2026-09-04T00:00:00Z";
const to = "2026-09-05T00:00:00Z";

export async function smoke(): Promise<void> {
    const slots: Slot[] = await listAvailability({ from, to });

    const firstAvailable = slots.find((slot) => slot.status === "available");
    if (!firstAvailable) {
        return;
    }

    const bookingRequest: BookingRequest = {
        slotId: firstAvailable.id,
        invitee: { name: "Jane Doe", email: "jane@example.com" },
    };
    const created: Booking = await createBooking(bookingRequest);

    const fetched: Booking = await getBooking(created.id);

    if (fetched.id !== created.id) {
        throw new Error("smoke: booking id mismatch");
    }
}
