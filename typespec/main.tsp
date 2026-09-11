import "@typespec/openapi3";
import "@typespec/http";

using TypeSpec.Http;

@service(#{
    title: "Call Calendar API",
})
namespace CallCalendar {
    @format("uuid")
    scalar uuid extends string;

    model Host {
        id: uuid;
        name: string;
        email: string;
    }

    model Invitee {
        name: string;
        email: string;
    }

    enum SlotStatus {
        available: "available";
        booked: "booked";
    }

    model Slot {
        id: uuid;
        start: utcDateTime;
        end: utcDateTime;
        durationMinutes: 30;
        hostId: uuid;
        status: SlotStatus;
    }

    enum BookingState {
        confirmed: "confirmed";
        canceled: "canceled";
    }

    model Booking {
        id: uuid;
        slotId: uuid;
        host: Host;
        invitee: Invitee;
        createdAt: utcDateTime;
        state: BookingState;
    }

    model BookingRequest {
        slotId: uuid;
        invitee: Invitee;
    }

    model ErrorEnvelope {
        code: string;
        message: string;
        details?: string;
    }

    model UnprocessableContentResponse is Response<422>;

    @route("/availability")
    @get
    op listAvailability(
        @query from: utcDateTime,
        @query to: utcDateTime,
    ): OkResponse & Body<Slot[]> | BadRequestResponse & Body<ErrorEnvelope> | UnprocessableContentResponse & Body<ErrorEnvelope>;

    @route("/bookings")
    @post
    op createBooking(
        @body body: BookingRequest,
    ): CreatedResponse & Body<Booking> | ConflictResponse & Body<ErrorEnvelope> | BadRequestResponse & Body<ErrorEnvelope> | UnprocessableContentResponse & Body<ErrorEnvelope>;

    @route("/bookings/{id}")
    @get
    op getBooking(
        @path id: uuid,
    ): OkResponse & Body<Booking> | NotFoundResponse & Body<ErrorEnvelope> | BadRequestResponse & Body<ErrorEnvelope>;
}
