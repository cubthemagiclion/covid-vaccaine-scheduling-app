use covidVaccineScheduling;
-- FINAL FINAL VERSION OF ALGORITHM

select * from PatientReceivedAppointment;

-- this is to make sure every time we run the algorithm, all the sent offers will become unavailable so that we do not send duplicate offers to different patients
DELIMITER ^^
DROP TRIGGER IF EXISTS sent_new_offer;
CREATE TRIGGER sent_new_offer AFTER INSERT ON PatientReceivedAppointment
FOR EACH ROW
BEGIN
UPDATE Appointment SET available_or_not = false where AppointmentId = NEW.AppointmentId;
END; ^^

DELIMITER ;

-- this is to make sure every time we manually delete a patient offer, the appointment will become available again.
DELIMITER $$
DROP TRIGGER IF EXISTS manually_delete__offer;
CREATE TRIGGER manually_delete__offer BEFORE DELETE ON PatientReceivedAppointment
FOR EACH ROW
BEGIN
UPDATE Appointment SET available_or_not = true where AppointmentId = OLD.AppointmentId;
END; $$

DELIMITER ; 


-- this is just a function I wrote so we can compare the date of two different dates and return the early one

DELIMITER \\
DROP FUNCTION IF EXISTS datecompare;
CREATE FUNCTION datecompare(
	date1 DATE,
    date2 DATE
)
RETURNS DATE
DETERMINISTIC
BEGIN
		IF  date1 < date2 THEN 
			RETURN date1;
		ELSE
			RETURN date2;
		END  IF;
END; \\

DELIMITER ;

-- this is the stored procedure that when you put a patient's ID, this will find all appointments that 
-- 1. the patient is eligible for 
-- 2. is the cloest to the patient 
-- 3. is the earliest possible.
DELIMITER ??
drop procedure if exists getAppointmentIDforEachPatient;
CREATE Procedure getAppointmentIDforEachPatient(
	 pid_in_function INT -- we get a patient's patient ID
)
BEGIN
DECLARE result_appointment_ID INT;
DECLARE appointment_date_in_function DATE;
DECLARE offerenddate DATE;
DECLARE numberOfAppReturned INT;
drop temporary table if exists t2;
create temporary table t2(
WITH ps AS -- ps is short for patient schedule, we have a timeSlot which is the start Time of patient's availability and we add 4 hours to it to get the endTime because in our system we are assuming each availability is a 4 hours slot.
(select `patientId`, timeSlot, addTime(timeSlot,'04:00:00') as endTime, day, max_travel_distance, patients_coordinates, g.Priority_Rank as priority, g.Eligible_date as e_date
from Calendar natural join PatientAvailability natural join patients as p natural join Priority_Group as g
where `patientId` = pid_in_function),-- this is where we put the patient ID
ss AS -- ss are the join table of providers and appointments but we convert -- appointment dates to weekday format where 0 represents Monday and 1 represents Tuesday and so on. 
(select providerId, AppointmentId, a.Date as Appointment_Date, WEEKDAY(Date) as day, startTime, providers_coordinates
from appointment as a natural join providers
where a.available_or_not = true)
SELECT appointmentId, ss.Appointment_Date, providerId, ps.day, ps.priority, ps.e_date, startTime, (ST_Distance_Sphere(providers_coordinates, patients_coordinates)/1600) as Distance, max_travel_distance
from ss join ps on ss.day = ps.day AND (ss.startTime between ps.timeSlot AND ps.endTime)
having Distance < max_travel_distance AND e_date <= ss.Appointment_Date
order by Distance asc, Appointment_Date asc limit 1);
set numberOfAppReturned = (select count(*) from t2);
IF numberOfAppReturned > 0 THEN
set result_appointment_ID = (select AppointmentId from t2);
SET appointment_date_in_function = (select Date from Appointment where AppointmentId = result_appointment_ID);
SET offerenddate = datecompare(DATE_ADD(appointment_date_in_function, INTERVAL -1 DAY),DATE_ADD(NOW(),INTERVAL 2 DAY)); -- we compare which one is smaller, either one day before the appointment or 2 days after today should be the deadline to accept the offer
-- if appointment is not null which means we find something that matches with patient, then we send the offer to the patient.
insert into PatientReceivedAppointment(patientId, AppointmentId, deadlineToAccept) values(pid_in_function, result_appointment_ID, offerenddate);
END IF;
drop temporary table t2;
End; ??

DELIMITER ;


-- in this procedure, we will find all patients who have not been assigned a an appointment and sort by 1. their priority group 2. their age (the bigger the earlier)
-- then we will loop through these people and call the getAppointmentIDforEachPatient function we created to insert the appointment for each patient.
DELIMITER !!
DROP Procedure if exists getPatientsByOrder;
CREATE Procedure getPatientsByOrder()
BEGIN
DECLARE i int default 0;
DECLARE size int;
drop temporary table if exists tt;
create temporary table tt(
with ns as(
with no_offer_patients as(
with t as(
WITH group_p as(
SELECT groupID, `patientId` FROM Patients WHERE groupID IS NOT NULL)
SELECT g.groupID, g.`patientID`, p.status, p.appointmentID
from group_p as g left outer join PatientReceivedAppointment as p on g.`patientID` = p.`patientID`)
select t1.`patientId`, groupID, status, appointmentID 
from t as t1
where t1.`patientId` not in
(select t2.`patientId` from t as t2
where (t2.status = "Offer Sent" OR t2.status = "Completed" OR t2.status = "Accepted")))
select distinct n.`patientId`, p.patient_name, p.patient_DOB, Eligible_date, g.Priority_Rank 
from no_offer_patients as n natural join patients as p natural join priority_group as g  -- we will not include those who do not have a group ID yet.
where g.Priority_Rank >= 1 -- this is for excluding those who does not have a group assigned yet.
order by Priority_Rank, p.patient_DOB) -- these patients will be ranked by priority rank then their birthday, the older patients will get appointments first.
select `patientId` from ns);
ALTER TABLE tt ADD validated INT DEFAULT 0;
ALTER TABLE tt ADD rowID INT AUTO_INCREMENT, ADD PRIMARY KEY (rowID);
  While exists(Select * From tt Where validated = 0) Do
    Select `patientId`, `rowID` Into @currentpatientID, @currentRowID 
    from tt Where validated = 0 Limit 1;
    CALL getAppointmentIDforEachPatient(@currentpatientID);
    Update tt
    Set validated = 1
    Where rowID = @currentRowID;
  END WHILE;
DROP TABLE tt;
END; !!

DELIMITER ;



CALL getPatientsByOrder(); -- this is what we need to run in order to match patients with appointments
select * from PatientReceivedAppointment;


-- insert into PatientReceivedAppointment  (patientId, AppointmentId, deadlineToAccept) values(2, 5, DATE_ADD(NOW(), INTERVAL 7 DAY));


