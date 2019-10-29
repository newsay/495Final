/* 

----- USERS VALUES ----- 
MANAGER = 1
USER = 0
ADMIN = 2

----- SHIFT VALUES -----
UNASSIGNED = 0
ASSIGNED = 1

----- REQUEST VALUES -----
*STATUS*
PENDING = 0
APPROVED = 1
DENIED = 2

*TYPE*
ASSIGNMENT = 0
CANCELATION = 1


/* ------------- Insert Organizations ----------------------- */

INSERT INTO Organizations (OrganizationID,OrganizationName,IsEnabled)
VALUES ('','UMUC', '1');

INSERT INTO Organizations (OrganizationID,OrganizationName,IsEnabled)
VALUES ('','Scheduling OnDemand', '1');


/* ------------- Insert Shifts ----------------------- */

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-0000','RN','89.50','2019-07-01','13:00','2019-07-01','22:00','Gender: Male, Language: Spanish Speaking, Experience: Vent');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-1122','LVN','75.99','2019-06-30','09:00','2019-06-30','23:00','Language: Spanish Speaking, Experience: Children under 2 yrs old');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-7744','RN/LVN','80.00','2019-07-02','17:00','2019-07-02','22:00','');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-1234','RN','94.80','2019-07-03','18:00','2019-07-04','03:00','Gender: Female');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-9999','LVN','78.00','2019-07-01','07:00','2019-07-01','17:00','Experience: Vent');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-1289','RN','69.00','2019-07-05','08:00','2019-07-05','13:00','');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-9876','LVN','59:87','2019-07-05','12:00','2019-07-05','16:00','');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-7777','RN/LVN','71:25','2019-07-08','16:00','2019-07-08','23:00','');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-1945','LVN','77:50','2019-07-11','23:00','2019-07-12','09:00','');

INSERT INTO Shifts (ShiftID,Status,OrganizationID,ZipCode,RequiredPosition,PayDifferential,StartDate,StartTime,EndDate,EndTime,SpecialRequirements)
VALUES ('','0','1','78254-5511','RN','80:25','2019-07-12','19:00','2019-07-13','03:00','');

/* ------------- Insert Assigned Shifts ----------------------- */

INSERT INTO Requests (RequestID,ShiftID,UserID,Status,Type)
VALUES ('','2','2','0','0');

INSERT INTO Requests (RequestID,ShiftID,UserID,Status,Type)
VALUES ('','4','4','0','0');

INSERT INTO Requests (RequestID,ShiftID,UserID,Status,Type)
VALUES ('','6','2','1','0');

INSERT INTO Requests (RequestID,ShiftID,UserID,Status,Type)
VALUES ('','8','4','2','0');

INSERT INTO Requests (RequestID,ShiftID,UserID,Status,Type)
VALUES ('','10','2','2','1');

INSERT INTO Requests (RequestID,ShiftID,UserID,Status,Type)
VALUES ('','1','4','1','1');