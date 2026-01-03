import { describe, expect, it } from "vitest";
import { getService } from "@/features/user/profileUtil";

describe("getService", () => {
  it("detects known services", () => {
    expect(getService("https://twitter.com/user")?.service).toBe("Twitter");
    expect(getService("https://x.com/user")?.service).toBe("Twitter");
    expect(getService("https://bsky.app/user")?.service).toBe("Bluesky");
  });

  it("returns undefined for unknown service", () => {
    expect(getService("https://example.com")).toBeUndefined();
  });
});
