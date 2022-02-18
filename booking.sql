USE bnb;

-- booking
DROP TABLE IF EXISTS booking;

CREATE TABLE IF NOT EXISTS booking (
    bookingID int unsigned NOT NULL auto_increment,
    customerID int unsigned NOT NULL,
    roomID int unsigned NOT NULL,
    checkindate date NOT NULL,
    checkoutdate date NOT NULL,
    contactnumber char(14) NOT NULL,
    bookingextras text NULL,
    breakfast varchar(14) NULL,
    roomreview text NULL,
    PRIMARY KEY (bookingID),
    FOREIGN KEY (customerID) REFERENCES customer(customerID),
    FOREIGN KEY (roomID) REFERENCES room(roomID)
) AUTO_INCREMENT = 1;

INSERT INTO
    `booking` (
        `bookingID`,
        `customerID`,
        `roomID`,
        `checkindate`,
        `checkoutdate`,
        `contactnumber`,
        `bookingextras`,
        `breakfast`,
        `roomreview`
    )
VALUES
    (
        '1',
        '1',
        '2',
        '2021-11-20',
        '2022-11-22',
        '(123) 456 7890',
        '',
        'continental',
        ''
    ),
    (
        '2',
        '2',
        '5',
        '2021-11-21',
        '2022-11-23',
        '(234) 567 8901',
        'non-smoking room plz',
        'cooked',
        ''
    ),
    (
        '3',
        '3',
        '2',
        '2022-03-21',
        '2022-03-22',
        '(345) 678 9012',
        '',
        'none',
        ''
    );