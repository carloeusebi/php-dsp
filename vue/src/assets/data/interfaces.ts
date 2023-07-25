export interface Patient {
	id?: number;
	fname: string;
	lname: string;
	age?: number;
	birthday: string;
	birthplace?: string;
	address?: string;
	fiscalcode?: string;
	begin: string;
	email?: string;
	phone?: string;
	consent?: string | File;
	weight?: string;
	height?: string;
	job?: string;
	sex?: 'M' | 'F' | 'O';
	cohabitants?: string;
	username: string;
}

export interface QuestionItem {
	id: number;
	text: string;
	answer?: number;
	comment?: string;
}

export interface QuestionLegend {
	id: number;
	legend: string;
}

export interface Question {
	id?: number;
	question: string;
	description: string;
	type: '' | '1-4' | '1-6' | '0-5' | '0-3' | '1-7' | '0-4' | '1-4' | '1-5';
	items: QuestionItem[];
	legend: QuestionLegend[];
	selected?: boolean;
	completed?: boolean;
}

export interface Survey {
	id?: number;
	patient_id: string;
	patient_name?: string;
	title: string;
	questions: Question[];
	created_at?: string;
	last_update?: string;
	completed: boolean;
	token?: string;
}

export interface Test {
	survey: Survey;
	patient: Patient;
}

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
