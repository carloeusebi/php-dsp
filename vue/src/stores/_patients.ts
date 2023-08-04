import { Patient } from '@/assets/data/interfaces';
import { deleteMixin, saveMixin } from '@/mixins';
import { defineStore } from 'pinia';

const endpoint = '/patients';

export const usePatientsStore = defineStore('patients', {
	//state
	state: () => ({
		patients: JSON.parse(localStorage.getItem('PATIENTS') as string) as Patient[],
		labels: JSON.parse(localStorage.getItem('PATIENT_LABELS') as string) as Patient,
	}),

	// getters
	getters: {
		getPatients: (state): Patient[] => state.patients,
		getLabels: (state): Patient => state.labels,
	},

	//actions
	actions: {
		fetch() {
			this.axios.get(endpoint).then(res => {
				this.load(res.data);
			});
		},

		/**
		 * Get a Patient by its ID
		 * @param id The Patient ID
		 * @returns The Patient object
		 */
		getById(id: number): Patient | undefined {
			return this.patients.find(patient => patient.id === id);
		},

		/**
		 * Loads both labels and patients list received from ta ajax call to the
		 * @param patients An object with labels and patients list
		 */
		load(patients: PatientsAndLabels) {
			this.loadPatients(patients.list);
			this.loadLabels(patients.labels);
		},

		/**
		 * Load the patients list
		 * @param patients the patient's list
		 */
		loadPatients(patients: Patient[]) {
			this.patients = patients;
			localStorage.setItem('PATIENTS', JSON.stringify(patients));
		},

		/**
		 * Load the patient's labels
		 * @param labels the user friendly labels in italian
		 */
		loadLabels(labels: Patient) {
			this.labels = labels;
			localStorage.setItem('PATIENT_LABELS', JSON.stringify(labels));
		},

		/**
		 * Calls the database to update the patient, if success it updates it locally
		 * @param patient The patient to be saved
		 */
		async save(patient: FormData): Promise<void> {
			return saveMixin(this, endpoint, patient, this.patients, this.loadPatients);
		},

		/**
		 * Calls the db to delete the patient, if success it deletes it locally
		 * @param id The ID of the patient to be deleted
		 */
		async delete(id: number) {
			await deleteMixin(this, endpoint, id, this.patients, this.loadPatients);
		},

		/**
		 * It updates the store deleting the file locally.
		 * @param patientId The ID of the Patient to whom the file is being deleted
		 * @param fileId The ID of the file to delete
		 */
		deletePatientFile(patientId: number, fileId: number) {
			const patIndex = this.patients.findIndex(({ id }) => id === patientId);
			const files = this.patients[patIndex].files;

			if (!files) return;

			this.patients[patIndex].files = this.patients[patIndex].files?.filter(({ id }) => id !== fileId);
		},
	},
});

export interface PatientsAndLabels {
	list: Patient[];
	labels: Patient;
}
