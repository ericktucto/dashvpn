CREATE TABLE server
(
    ip         TEXT    NOT NULL,
    listenPort INTEGER NOT NULL,
    address    TEXT    NOT NULL,
    dns        TEXT    NOT NULL,
    interface  TEXT    NOT NULL,
    postUp     TEXT    NOT NULL,
    postDown   TEXT    NOT NULL
)
