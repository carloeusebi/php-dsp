import { Patient, Question, Survey } from './interfaces';

//TODO to update

export const emptyQuestion: Omit<Question, 'id'> = {
	question: '',
	description: '',
	type: '',
	legend: [],
	items: [],
	variables: [],
};

export const emptyPatient: Partial<Patient> = {
	fname: '',
	lname: '',
	birthday: '',
	begin: '',
};

export const emptySurvey: Partial<Survey> = {
	title: '',
	completed: false,
};

export const questionTypes = ['1-4', '1-6', '0-5', '0-3', '1-7', '0-4', '1-5', 'EDI', 'MUL'];
