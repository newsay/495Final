-- Authored by Andrew Ritchie
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Organizations;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Shifts;
DROP TABLE IF EXISTS ShiftAuditTrail;
DROP TABLE IF EXISTS Request;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE Organizations (
  OrganizationID INT NOT NULL AUTO_INCREMENT,
  OrganizationName VARCHAR(100),
  IsEnabled BIT NOT NULL,
  PRIMARY KEY (OrganizationID)
);

CREATE TABLE Users (
   UserID INT NOT NULL AUTO_INCREMENT,
   OrganizationID INT,
   UserType INT,
   Password VARCHAR(255),
   Email VARCHAR(50),
   FirstName VARCHAR(20),
   LastName VARCHAR(20),
   HomePhone VARCHAR(20),
   MobilePhone VARCHAR(20),
   Address1 VARCHAR(50),
   Address2 VARCHAR(50),
   City VARCHAR(25),
   State VARCHAR(20),
   ZipCode VARCHAR(10),
   SecurityQuestion1 INT,
   SecurityQuestion1Answer VARCHAR(255),
   SecurityQuestion2 INT,
   SecurityQuestion2Answer VARCHAR(255),
   SecurityQuestion3 INT,
   SecurityQuestion3Answer VARCHAR(255),
   FOREIGN KEY (OrganizationID) REFERENCES Organizations(OrganizationID),
   PRIMARY KEY (UserID)
);

CREATE TABLE Shifts (
    ShiftID INT NOT NULL AUTO_INCREMENT,
    Status INT,
    UserID INT,
    OrganizationID INT,
    ZipCode VARCHAR(10),
    RequiredPosition VARCHAR(20),
    PayDifferential DECIMAL(5,2),
    StartDate DATE,
    StartTime TIME,
    EndDate DATE,
    EndTime TIME,
    SpecialRequirements VARCHAR(250),
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (OrganizationID) REFERENCES Organizations(OrganizationID),
    PRIMARY KEY (ShiftID)
);

CREATE TABLE ShiftAuditTrail (
    AuditID INT NOT NULL AUTO_INCREMENT,
    ModificationDate DATETIME,
    ShiftID INT,
    Details VARCHAR(1023),
    FOREIGN KEY (ShiftID) REFERENCES Shifts(ShiftID),
    PRIMARY KEY (AuditID)
);

CREATE TABLE Requests (
    RequestID INT NOT NULL AUTO_INCREMENT,
    ShiftID INT NOT NULL,
    UserID INT NOT NULL,
    Status INT,
    Type INT,
    FOREIGN KEY (ShiftID) REFERENCES Shifts(ShiftID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    PRIMARY KEY (RequestID)
);