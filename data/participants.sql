DROP TABLE IF EXISTS "participants";
CREATE TABLE "participants" (
	 "email" text NOT NULL,
	 "fullname" text NOT NULL,
	 "vouchercode" text NOT NULL,
	PRIMARY KEY("email")
);
