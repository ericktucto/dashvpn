CREATE TABLE sharedlinks
(
    slug     VARCHAR(255) NOT NULL,
    otp      CHAR(6)      NOT NULL,
    contents TEXT         NOT NULL,
    exp      TIMESTAMP    NOT NULL
);
