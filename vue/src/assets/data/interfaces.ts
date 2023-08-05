import { usePatientsStore, useQuestionsStore, useSurveysStore } from '@/stores';

export interface AppFile {
	[foreignId: string]: any;
	id: number;
	name: string;
	type: string;
	uploaded_on: string;
}

export interface PatientFile extends AppFile {
	patient_id: number;
}

export interface Patient {
	id: number;
	fname: string;
	lname: string;
	age?: number;
	birthday: string;
	birthplace?: string;
	address?: string;
	codice_fiscale?: string;
	begin: string;
	email?: string;
	phone?: string;
	files?: PatientFile[];
	weight?: string;
	height?: string;
	job?: string;
	qualification?: string;
	sex?: 'M' | 'F' | 'O';
	cohabitants?: string;
}

export interface QuestionItemI {
	id: number;
	text: string;
	answer?: number;
	comment?: string;
	reversed?: boolean;
}

export interface QuestionLegend {
	id: number;
	legend: string;
}

export interface QuestionVariableCutoff {
	id: number;
	name: string;
	type: 'range' | 'greater-than';
	from: number;
	to: number;
}

export interface QuestionVariableI {
	id: number;
	name: string;
	items: number[]; //array of items ID
	cutoffs: QuestionVariableCutoff[];
}

export interface Question {
	id: number;
	question: string;
	description: string;
	type: '' | '1-4' | '1-6' | '0-5' | '0-3' | '1-7' | '0-4' | '1-4' | '1-5';
	legend: QuestionLegend[];
	items: QuestionItemI[];
	variables: QuestionVariableI[];
	selected?: boolean;
	completed?: boolean;
}

export interface Survey extends Patient {
	patient_id: number;
	patient_name?: string;
	title: string;
	questions: Question[];
	created_at?: string;
	last_update?: string;
	completed: boolean;
	token: string;
}

export type NewPatient = Omit<Patient, 'id'>;
export type NewQuestion = Omit<Question, 'id'>;
export type NewSurvey = Omit<Survey, 'id' | 'token'>;

export interface LoginForm {
	username: string;
	password: string;
}

export interface Cell {
	label: string;
	key: string;
}

export interface Order {
	by: string;
	type: 'up' | 'down';
}

export interface Errors {
	[string: string]: string;
}

type PatientsStore = ReturnType<typeof usePatientsStore>;
type QuestionStore = ReturnType<typeof useQuestionsStore>;
type SurveyStore = ReturnType<typeof useSurveysStore>;

export type MyStore = PatientsStore | QuestionStore | SurveyStore;
