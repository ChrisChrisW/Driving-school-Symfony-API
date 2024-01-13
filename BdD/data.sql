DROP TABLE IF EXISTS Vehicle ;
CREATE TABLE Vehicle (id_Vehicle SERIAL NOT NULL,
numPlate_Vehicle VARCHAR,
purchaseDate_Vehicle DATE,
power_Vehicle INT,
PRIMARY KEY (id_Vehicle));

DROP TABLE IF EXISTS Formula ;
CREATE TABLE Formula (id_Formula SERIAL NOT NULL,
wording_Formula VARCHAR,
slug_Formula VARCHAR,
price_Formula INT,
drivingformula_id_drivingformula **NOT FOUND**,
PRIMARY KEY (id_Formula));

DROP TABLE IF EXISTS Candidate ;
CREATE TABLE Candidate (id_Candidate SERIAL NOT NULL,
email_Candidate VARCHAR,
address_Candidate VARCHAR,
age_Candidate INT,
PRIMARY KEY (id_Candidate));

DROP TABLE IF EXISTS Trainer ;
CREATE TABLE Trainer (id_Trainer SERIAL NOT NULL,
numSs_Trainer BIGINT,
PRIMARY KEY (id_Trainer));

DROP TABLE IF EXISTS DrivingFormula ;
CREATE TABLE DrivingFormula (id_DrivingFormula SERIAL NOT NULL,
nbHours_DrivingFormula INT,
formula_id_formula **NOT FOUND**,
PRIMARY KEY (id_DrivingFormula));

DROP TABLE IF EXISTS DrivingFormulaHasVehicle ;
CREATE TABLE DrivingFormulaHasVehicle (id_Vehicle **NOT FOUND** NOT NULL,
id_DrivingFormula **NOT FOUND** NOT NULL,
PRIMARY KEY (id_Vehicle,
 id_DrivingFormula));

DROP TABLE IF EXISTS TrainerHasFormula ;
CREATE TABLE TrainerHasFormula (id_Formula **NOT FOUND** NOT NULL,
id_Trainer **NOT FOUND** NOT NULL,
PRIMARY KEY (id_Formula,
 id_Trainer));

DROP TABLE IF EXISTS User_(UserHasTrainerOrHasCandidate) ;
CREATE TABLE User_(UserHasTrainerOrHasCandidate) (id_Trainer **NOT FOUND** NOT NULL,
id_Candidate **NOT FOUND** NOT NULL,
lastName_User VARCHAR,
firstName_User VARCHAR,
PRIMARY KEY (id_Trainer,
 id_Candidate));

DROP TABLE IF EXISTS courseDates ;
CREATE TABLE courseDates (id_Trainer **NOT FOUND** NOT NULL,
id_Candidate **NOT FOUND** NOT NULL,
id_Vehicle **NOT FOUND** NOT NULL,
id_Formula **NOT FOUND** NOT NULL,
isAchieve_candidateHasFormulaAndTakesCourses BOOL,
isConfirm_candidateHasFormulaAndTakesCourses BOOL,
isRedirectedToAnotherTrainer_candidateHasFormulaAndTakesCourses BOOL,
startDate_candidateHasFormulaAndTakesCourses TIMESTAMP,
endDate_candidateHasFormulaAndTakesCourses TIMESTAMP,
PRIMARY KEY (id_Trainer,
 id_Candidate,
 id_Vehicle,
 id_Formula));

DROP TABLE IF EXISTS FormulaCodeDate_(CandidateHasCodeFormula) ;
CREATE TABLE FormulaCodeDate_(CandidateHasCodeFormula) (id_Candidate **NOT FOUND** NOT NULL,
id_Formula **NOT FOUND** NOT NULL,
startDate_FormulaCodeDate DATE,
endDate_FormulaCodeDate DATE,
PRIMARY KEY (id_Candidate,
 id_Formula));

DROP TABLE IF EXISTS CandidateHasDrivingFormula ;
CREATE TABLE CandidateHasDrivingFormula (id_DrivingFormula **NOT FOUND** NOT NULL,
id_Candidate **NOT FOUND** NOT NULL,
PRIMARY KEY (id_DrivingFormula,
 id_Candidate));

ALTER TABLE Formula ADD CONSTRAINT FK_Formula_drivingformula_id_drivingformula FOREIGN KEY (drivingformula_id_drivingformula) REFERENCES DrivingFormula (id_DrivingFormula);

ALTER TABLE DrivingFormula ADD CONSTRAINT FK_DrivingFormula_formula_id_formula FOREIGN KEY (formula_id_formula) REFERENCES Formula (id_Formula);
ALTER TABLE DrivingFormulaHasVehicle ADD CONSTRAINT FK_DrivingFormulaHasVehicle_id_Vehicle FOREIGN KEY (id_Vehicle) REFERENCES Vehicle (id_Vehicle);
ALTER TABLE DrivingFormulaHasVehicle ADD CONSTRAINT FK_DrivingFormulaHasVehicle_id_DrivingFormula FOREIGN KEY (id_DrivingFormula) REFERENCES DrivingFormula (id_DrivingFormula);
ALTER TABLE TrainerHasFormula ADD CONSTRAINT FK_TrainerHasFormula_id_Formula FOREIGN KEY (id_Formula) REFERENCES Formula (id_Formula);
ALTER TABLE TrainerHasFormula ADD CONSTRAINT FK_TrainerHasFormula_id_Trainer FOREIGN KEY (id_Trainer) REFERENCES Trainer (id_Trainer);
ALTER TABLE User_(UserHasTrainerOrHasCandidate) ADD CONSTRAINT FK_User_(UserHasTrainerOrHasCandidate)_id_Trainer FOREIGN KEY (id_Trainer) REFERENCES Trainer (id_Trainer);
ALTER TABLE User_(UserHasTrainerOrHasCandidate) ADD CONSTRAINT FK_User_(UserHasTrainerOrHasCandidate)_id_Candidate FOREIGN KEY (id_Candidate) REFERENCES Candidate (id_Candidate);
ALTER TABLE courseDates ADD CONSTRAINT FK_courseDates_id_Trainer FOREIGN KEY (id_Trainer) REFERENCES Trainer (id_Trainer);
ALTER TABLE courseDates ADD CONSTRAINT FK_courseDates_id_Candidate FOREIGN KEY (id_Candidate) REFERENCES Candidate (id_Candidate);
ALTER TABLE courseDates ADD CONSTRAINT FK_courseDates_id_Vehicle FOREIGN KEY (id_Vehicle) REFERENCES Vehicle (id_Vehicle);
ALTER TABLE courseDates ADD CONSTRAINT FK_courseDates_id_Formula FOREIGN KEY (id_Formula) REFERENCES Formula (id_Formula);
ALTER TABLE FormulaCodeDate_(CandidateHasCodeFormula) ADD CONSTRAINT FK_FormulaCodeDate_(CandidateHasCodeFormula)_id_Candidate FOREIGN KEY (id_Candidate) REFERENCES Candidate (id_Candidate);
ALTER TABLE FormulaCodeDate_(CandidateHasCodeFormula) ADD CONSTRAINT FK_FormulaCodeDate_(CandidateHasCodeFormula)_id_Formula FOREIGN KEY (id_Formula) REFERENCES Formula (id_Formula);
ALTER TABLE CandidateHasDrivingFormula ADD CONSTRAINT FK_CandidateHasDrivingFormula_id_DrivingFormula FOREIGN KEY (id_DrivingFormula) REFERENCES DrivingFormula (id_DrivingFormula);
ALTER TABLE CandidateHasDrivingFormula ADD CONSTRAINT FK_CandidateHasDrivingFormula_id_Candidate FOREIGN KEY (id_Candidate) REFERENCES Candidate (id_Candidate);
