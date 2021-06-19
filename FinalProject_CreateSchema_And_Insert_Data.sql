DROP DATABASE IF EXISTS covidVaccineScheduling;
CREATE DATABASE covidVaccineScheduling;
Use covidVaccineScheduling;


DROP TABLE IF EXISTS users;
CREATE TABLE users(
		user_id int(11) NOT NULL AUTO_INCREMENT,
	    username varchar(50) UNIQUE NOT NULL,
        password varchar(128) NOT NULL,
		account_type varchar(50) NULL,
        PRIMARY KEY(user_id))ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;
        
        
DROP TABLE IF EXISTS Priority_Group;
CREATE TABLE Priority_Group(
		groupId INT UNIQUE NOT NULL AUTO_INCREMENT,
        Eligible_date DATE NOT NULL,
        Eligible_requirements TEXT,
        Priority_Rank INT NOT NULL,
        PRIMARY KEY(groupId) );
        
DROP TABLE IF EXISTS Patients;
CREATE TABLE Patients(
		patientId INT UNIQUE NOT NULL AUTO_INCREMENT,
        patient_name varchar(50) NOT NULL,
        patient_DOB date NOT NULL,
        ssn int(9) NOT NULL,
        patient_address varchar(128) NOT NULL,
        patient_phone varchar(20) NOT NULL,
        patient_email varchar(50) NOT NULL,
        max_travel_distance INT NOT NULL,
        user_id int(11) NOT NULL,
        groupId INT DEFAULT NULL,
        document BLOB NULL,
        patients_coordinates point NULL,
        PRIMARY KEY(patientId),
        FOREIGN KEY(user_id) REFERENCES  users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (groupId) REFERENCES Priority_Group(groupId) ON DELETE CASCADE ON UPDATE CASCADE);
        
DROP TABLE IF EXISTS Providers;
CREATE TABLE Providers(
		providerId INT UNIQUE NOT NULL AUTO_INCREMENT,
        provider_name varchar(50) NOT NULL,
        provider_phone varchar(20) NOT NULL,
        provider_address varchar(128) NOT NULL,
        providerType varchar(50) NOT NULL,
        user_id int(11) NOT NULL,
        providers_coordinates point NULL,
        PRIMARY KEY (providerId),
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE);
        
DROP TABLE IF EXISTS Calendar;
CREATE TABLE Calendar(
		timeSlotId INT UNIQUE NOT NULL AUTO_INCREMENT,
        timeSlot time NOT NULL,
        day int NOT NULL,
        PRIMARY KEY (timeSlotId) );
        
DROP TABLE IF EXISTS Appointment;
CREATE TABLE Appointment(
		AppointmentId INT UNIQUE NOT NULL AUTO_INCREMENT,
        Date date NOT NULL,
        startTime time NOT NULL,
        providerId INT NOT NULL,
        available_or_not bool NOT NULL DEFAULT true,
        PRIMARY KEY(AppointmentId),
        FOREIGN KEY (providerId) REFERENCES Providers(providerId) ON DELETE CASCADE ON UPDATE CASCADE);
        
DROP TABLE IF EXISTS PatientAvailability;
CREATE TABLE PatientAvailability(
		patientId INT NOT NULL,
        timeSlotId INT NOT NULL,
        PRIMARY KEY (patientId, timeSlotId),
        FOREIGN KEY (patientId) REFERENCES Patients(patientId) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (timeSlotId) REFERENCES Calendar(timeSlotId) ON DELETE CASCADE ON UPDATE CASCADE);
        

DROP TABLE IF EXISTS PatientReceivedAppointment;
CREATE TABLE PatientReceivedAppointment(
		patientId INT  NOT NULL,
        AppointmentId INT  NOT NULL auto_increment,
        status varchar(50) NOT NULL DEFAULT "Offer Sent",
        offerSentTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        deadlineToAccept datetime NOT NULL,
        responseReceivedTime timestamp NULL DEFAULT NULL,
        PRIMARY KEY(patientId, AppointmentId),
        FOREIGN KEY (patientId) REFERENCES Patients(patientId) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (AppointmentId) REFERENCES Appointment(AppointmentId) ON DELETE CASCADE ON UPDATE CASCADE);


-- Calendar Data Insertion
insert into Calendar 
values 
(1, "08:00:00", 0),
(2, "12:00:00", 0),
(3, "08:00:00", 1),
(4, "12:00:00", 1),
(5, "08:00:00", 2),
(6, "12:00:00", 2),
(7, "08:00:00", 3),
(8, "12:00:00", 3),
(9, "08:00:00", 4),
(10, "12:00:00", 4),
(11, "08:00:00", 5),
(12, "12:00:00", 5),
(13, "08:00:00", 6),
(14, "12:00:00", 6)
;


-- Priority Group Insertion:

INSERT INTO Priority_Group(groupId, Eligible_date, Eligible_requirements, Priority_Rank) VALUES 
(1,  '2021-01-01', 'health care workers + residents of longer-term care facilities', 1),
(2,  '2021-02-01', 'age >= 75 + essential workers', 2),
(3, '2021-03-01', '65-74 + high risk medical conditions',3),
(4, '2021-04-01', '16-64 adults', 4);



-- Users login for Patients Insertion :
insert into users (username, account_type, password)
values 
("codyJacobs", "patient", "mycatname"),
("marieLemar", "patient", "somethingCool"),
("declanMcFry", "patient", "password"),
("jessicaJohnson", "patient", "easypass"),
("juanFlores", "patient", "porkchop"),
("markJuventud", "patient", "pineapple"),
("melanieRamsay", "patient", "coolgirl"),
("johnDrerer", "patient", "vaccine"),
("guidoTerra", "patient", "anything"),
("bobMell", "patient", "imbored")

; 


-- Patients insertion:
INSERT INTO Patients(patientId, patient_name, patient_DOB, ssn, patient_address, patient_phone, patient_email, max_travel_distance, user_id,patients_coordinates) VALUES 
(1, "Cody Jacobs", "1984-08-24", 132453985, "7505 Glenlake St. Brooklyn, NY 11201", "15166789544", "codyj3@gmail.com", 5, 1, point(40.693854, -73.977167)),
(2,  "Marie Lemar" , '1943-12-14',114673985,"7857 Pendergast Street New York, NY 10031", '12124568603', 'mlemar34@gmail.com', 2, 2, point(40.826675, -73.948085)),
(3,  "Declan McFry" , '1965-04-07',124675752,"9221 Griffin Court Brooklyn, NY 11224", '19172648694', 'ddog75@gmail.com', 7, 3,point(40.766903, -73.990108)),
(4,  "Jessica Johnson" , '1999-07-28',132594913,"775 Newcastle Rd. New York, NY 10024", '12129778233', 'jjcool55@gmail.com', 4, 4,point(40.786053, -73.974401)),
(5,  "Juan Flores" , '1972-01-04',139365932,"215 NW. Woodside Ave. Flushing, NY 11355", '13478425924', 'juan1234@gmail.com', 6, 5,point(40.722853, -73.746545)),
(6,  "Mark Juventud" , '1981-10-31',1246533555,"686 Border Rd. Bronx, NY 10457", '17187863463', 'themark1@gmail.com', 12, 6,point(40.846660, -73.897491)),
(7,  "Melanie Ramsay" , '1975-11-20',121256378,"506 West Colonial Ave. Brooklyn, NY 11234", '13471057942', 'melram934@gmail.com', 4, 7,point(40.630951, -74.033826)),
(8,  "John Drerer" , '1929-02-23',111769419,"9528 Jefferson St. New York, NY 10033", '17187626835', 'johnD@gmail.com', 8, 8,point(40.702855, -73.927907)),
(9,  "Guido Terra" , '1955-09-02',124614598,"395 Plumb Branch Dr. Brooklyn, NY 11210", '13471449086', 'guido456@gmail.com', 6, 9,point(40.627975, -73.945704)),
(10,  "Bob Mell" , '1947-04-20',124901237,"478 Vermont Road Bronx, NY 10472", '13471498512', 'bobbyMell1@gmail.com', 12, 10,point(40.66765863813709, -73.89307108179862))
;

-- login info for providers
insert into users (username, account_type, password) values
('javits','provider','javitsIsTheBest'),
('medgar','provider','medgarIsTheBest'),
('amnh','provider','amnhHasBlueWhale'),
('goYankees','provider','YankeesSucks'),
('yorkcollege','provider','yorky'),
('walgreens14155','provider','weHateOurJobHere'),
('walgreens14130','provider','weHateOurJobToo'),
('cvs10826','provider','cvsIsTheBestNoItsNot'),
('nyulangone','provider','goViolets'),
('cog','provider','zheliquanshishabilaide')
;
-- insert providers
insert into Providers (provider_name, provider_phone,provider_address,providerType,user_id, providers_coordinates) values
("JACOB K. JAVITS CONVENTION CENTER OF NEW YORK", 2122162000, "429 11th Avenue, New York, NY 10001", "convention center", 11,point(40.757927008494256, -74.00235858851624)),
("Medgar Evers College", 7182704900, "231 Crown Street, Brooklyn, NY 11225", "college",12,point(40.66683234215505, -73.95239320016293)),
("American Museum of Natural History", "212-769-5100","200 Central Park West, New York, NY 10024-5102","museum",13,point(40.78142097010712, -73.97396625543279)),
("Yankee Stadium", "(718) 293-4300","1 E 161 St, The Bronx, NY 10451","stadium",14,point(40.82974811375358, -73.926238875021)),
("York College", "718-262-2050.","160-2 Liberty Avenue, Jamaica, NY 11451", "college",15,point(40.70027545018531, -73.7965306308467)),
("Walgreens Co. #14155","(212) 760-8107","2 Pennsylvania Plaza, New York, NY 10121","pharmacy",16,point(40.75017754030301, -73.99218114433846)),
("Walgreens Co. #14130","(212) 683-5532","4 Park Ave, New York, NY 10016","pharmacy",17,point(40.74714259569332, -73.9819536443386)),
("CVS Pharmacy, Inc. #10826","(212) 221-3844","1440 Broadway, New York, NY 10018","pharmacy",18,point(40.7545253151482, -73.98642081550278)),
("NYU Langone Health","646-929-7870","550 First Avenue, New York, NY 10016","hospital",19,point(40.74218988046599, -73.97378672899627)),
("Church of God East New York","(877) 829-4692","905 Sutter Avenue, Brooklyn, 11207","commmunity center",20,point(40.67137080149206, -73.8847835001628));

insert into Appointment(date, startTime, providerId) values ("2021-04-26",'10:30:00',1),
("2021-03-16",'09:30:00',2),
("2021-01-01",'11:00:00',1),
("2021-05-01",'12:00:00',1),
("2021-07-18",'08:00:00',1),
("2021-02-14",'13:00:00',2),
("2021-02-22",'15:30:00',2),
("2021-08-13",'11:00:00',3),
("2021-02-26",'14:20:00',5),
("2022-05-24",'15:00:00',2),
("2021-04-18",'11:00:00',1),
("2021-01-24",'11:00:00',9);

insert into PatientAvailability values (1,1),
(1,2),
(1,4),
(1,6),
(1,9),
(1,12),
(1,11),
(2,1),
(2,3),
(2,14),
(3,13),
(3,3),
(3,7),
(3,8),
(4,1),
(4,10),
(4,6),
(9,1),
(10,2),
(5,3),
(5,8),
(5,10),
(5,5),
(6,3),
(6,9),
(6,7),
(6,12),
(7,5),
(7,6),
(7,11),
(8,14),
(8,1),
(8,2),
(8,8)
;

DELIMITER //
CREATE EVENT IF NOT EXISTS auto_decline
ON SCHEDULE
    EVERY 10 SECOND
DO
BEGIN
    IF deadlineToAccept BETWEEN offerSentTime AND CURDATE()
    THEN
    UPDATE PatientReceivedAppointment SET status = 'Declined';
END IF;
END; //
DELIMITER ;


DROP TRIGGER IF EXISTS received_update_from_patient;
DELIMITER //
CREATE TRIGGER received_update_from_patient BEFORE UPDATE ON PatientReceivedAppointment
FOR EACH ROW
BEGIN
-- set @new_status = (select responseReceivedTime from PatientReceivedAppointment as p where p.patientId = NEW.patientId and p.appointmentId = NEW.appointmentId);
IF
NEW.status = 'Accepted'
THEN
SET NEW.responseReceivedTime = CURRENT_TIMESTAMP();
END IF;
END; //
DELIMITER ;


DROP TRIGGER IF EXISTS sent_new_offer;
DELIMITER //
CREATE TRIGGER sent_new_offer AFTER INSERT ON PatientReceivedAppointment
FOR EACH ROW
BEGIN
UPDATE Appointment SET available_or_not = false where AppointmentId = NEW.AppointmentId;
END; //
DELIMITER ;





DROP TRIGGER IF EXISTS patient_declined_appointment;
DELIMITER //
CREATE TRIGGER patient_declined_appointment BEFORE UPDATE ON PatientReceivedAppointment
FOR EACH ROW
BEGIN
IF
(NEW.status = 'Declined'
OR
NEW.status = 'Cancelled')
THEN
SET NEW.responseReceivedTime = CURRENT_TIMESTAMP();
UPDATE Appointment SET available_or_not = true where AppointmentId = NEW.AppointmentId;
END IF;
END; //
DELIMITER ;

-- select * from PatientAvailability natural join patients natural join calendar;
insert into PatientReceivedAppointment (patientId,AppointmentId, deadlineToAccept) values (2,1,"2021-04-26 00:00:00");
update PatientReceivedAppointment 
set	status = 'Accepted' 
WHERE patientId = 2 AND AppointmentId = 1;
update PatientReceivedAppointment 
set	status = 'Completed' 
WHERE patientId = 2 AND AppointmentId = 1;

insert into PatientReceivedAppointment (patientId,AppointmentId, deadlineToAccept) values (4,4,"2021-04-25 18:34:00");
update PatientReceivedAppointment 
set	status = 'Declined' 
WHERE patientId = 4 AND AppointmentId = 4;
insert into PatientReceivedAppointment (patientId,AppointmentId, deadlineToAccept) values (9,4,"2021-04-27 00:00:00");

insert into PatientReceivedAppointment (patientId,AppointmentId, deadlineToAccept) values 
(2,2,"2021-03-22 00:00:00"),
(2,3,"2021-02-02 00:00:00"),
(2,6,"2021-02-23 00:00:00")
;
update PatientReceivedAppointment 
set	status = 'Accept' 
WHERE patientId = 2 AND AppointmentId = 2;
update PatientReceivedAppointment 
set	status = 'Cancelled' 
WHERE patientId = 2 AND AppointmentId = 2;
update PatientReceivedAppointment 
set	status = 'Accept' 
WHERE patientId = 2 AND AppointmentId = 3;
update PatientReceivedAppointment 
set	status = 'Cancelled' 
WHERE patientId = 2 AND AppointmentId = 3;
update PatientReceivedAppointment 
set	status = 'Accept' 
WHERE patientId = 2 AND AppointmentId = 6;
update PatientReceivedAppointment 
set	status = 'Cancelled' 
WHERE patientId = 2 AND AppointmentId = 6;

insert into PatientReceivedAppointment (patientId,AppointmentId, deadlineToAccept) values 
(3,5,"2021-05-22 00:00:00");
update PatientReceivedAppointment 
set	status = 'Accepted' 
WHERE patientId = 3 AND AppointmentId = 5;

insert into PatientReceivedAppointment (patientId,AppointmentId, deadlineToAccept) values 
(5,7,"2021-01-22 00:00:00");
update PatientReceivedAppointment 
set	status = 'Accepted' 
WHERE patientId = 5 AND AppointmentId = 7;
update PatientReceivedAppointment 
set	status = 'No Show' 
WHERE patientId = 5 AND AppointmentId = 7;

insert into PatientReceivedAppointment (patientId,AppointmentId, deadlineToAccept) values 
(5,9,"2021-01-26 00:00:00");
update PatientReceivedAppointment 
set	status = 'Accepted' 
WHERE patientId = 5 AND AppointmentId = 9;
update PatientReceivedAppointment 
set	status = 'No Show' 
WHERE patientId = 5 AND AppointmentId = 9;

insert into PatientReceivedAppointment (patientId,AppointmentId, deadlineToAccept) values 
(7,8,"2021-07-13 00:00:00");
update PatientReceivedAppointment 
set	status = 'Accepted' 
WHERE patientId = 7 AND AppointmentId = 8;

update Patients set groupId = 1 where patientId = 1; 
update Patients set groupId = 1 where patientId = 2; 
update Patients set groupId = 4 where patientId = 3; 
update Patients set groupId = 3 where patientId = 4;
update Patients set groupId = 2 where patientId = 5;
update Patients set groupId = 4 where patientId = 6;
update Patients set groupId = 4 where patientId = 7;
update Patients set groupId = 2 where patientId = 8;
update Patients set groupId = 3 where patientId = 9;
update Patients set groupId = 3 where patientId = 10;

INSERT into users (username, account_type, password) values ('john_chen','patient','chenyongshinidie');
INSERT INTO Patients(patient_name, patient_DOB, ssn, patient_address, patient_phone, patient_email, max_travel_distance, user_id) VALUES 
("John Chen", "1971-07-18", 650239388, "6 MetroTech Center, Brooklyn, NY 11201", "646-997-3600", "johnchen@gmail.com", 2, 21);




select * from appointment;