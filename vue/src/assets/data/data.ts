import { Patient, Question, Survey } from './interfaces';

export const emptyQuestion: Question = {
	question: '',
	description: '',
	type: '',
	answers: [],
	legend: [],
};

export const emptyPatient: Patient = {
	fname: '',
	lname: '',
	age: '',
	birthday: '',
	begin: '',
	username: '',
};

export const emptySurvey: Survey = {
	patient_id: '',
	title: '',
	survey: [],
	answers: [],
	completed: false,
};

export const questionTypes = ['1-4', '1-6', '0-5', '0-3', '1-7', '0-4', '1-5'];
