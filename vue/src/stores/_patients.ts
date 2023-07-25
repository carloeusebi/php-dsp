import { Patient } from '@/assets/data/interfaces';
import { defineStore } from 'pinia';

const endpoint = '/patients';

export const usePatientsStore = defineStore('patients', {
	//state
	state: () => ({
		patients: JSON.parse(
			localStorage.getItem('PATIENTS') as string
		) as Patient[],
		labels: JSON.parse(
			localStorage.getItem('PATIENT_LABELS') as string
		) as Patient,
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
		 * Loads both labels and patients list received from ta ajax call to the
		 * @param patients An object with labels and patients list
		 */
		load(patients: PatientsAndLabels) {
			this.loadPatients(patients.list);
			this.loadLabels(patients.labels);
		},

		/**
		 * Given the id returns the patient with that id
		 * @param id The id
		 * @returns the found patient, or null
		 */
		getById(id: string): Patient | undefined {
			return this.patients.find(p => String(p.id) == id);
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
		 * @param the patient object
		 * @returns the result of the ajax call
		 */
		async save(patient: FormData) {
			return this.axios
				.post(endpoint, patient)
				.then(res => {
					const newPatient = res.data.patient;

					const indexToUpdate = this.patients.findIndex(
						({ id }) => id == newPatient.id
					);

					if (indexToUpdate === -1) {
						//is new patient => add new patient to patients array
						this.patients.push(newPatient);
					} else {
						//is not new patient? => update patient
						this.patients[indexToUpdate] = newPatient;
					}
					this.loadPatients(this.patients);
				})
				.catch(e => {
					throw e;
				});
		},

		/**
		 * Calls the db to delete the patient, if success it deletes it locally
		 */
		async delete(id: number) {
			return this.axios
				.delete(endpoint, { data: { id } })
				.then(() => {
					//delete patient from local store
					const filteredPatients = [...this.patients.filter(p => p.id !== id)];
					this.loadPatients(filteredPatients);
				})
				.catch(e => {
					throw e;
				});
		},
	},
});

export interface PatientsAndLabels {
	list: Patient[];
	labels: Patient;
}
