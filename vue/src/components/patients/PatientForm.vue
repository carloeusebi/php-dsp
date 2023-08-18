<script lang="ts" setup>
import { Ref, ref, watch } from 'vue';
import AppInputElement from '@/components/AppInputElement.vue';
import { Patient } from '@/assets/data/interfaces';
import { emptyPatient } from '@/assets/data/data';

interface Props {
	patient: Patient;
	isTest?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
	isTest: false,
});

// const props = defineProps<Props>();

const emit = defineEmits(['form-emptied']);
const form: Ref<Patient> = ref(props.patient);

/**
 * Empties all form's fields
 */
const emptyFields = () => {
	form.value = emptyPatient;
	emit('form-emptied');
};

/**
 * Given a string it capitalizes every word in the string, marco di corato => Marco Di Corato
 * @param {string} str the string to capitalize
 */
const capitalize = (str: string): string => {
	return (str.charAt(0).toUpperCase() + str.slice(1).toLowerCase()).replace(/(^|\s|')\w/g, match =>
		match.toUpperCase()
	);
};

/********* WATCHERS *******************/

const updateField = (field: string, updatedValue: string): void => {
	if (!field || !updatedValue || (field !== 'fname' && field !== 'lname')) return;
	form.value[field] = capitalize(updatedValue).toString();
};

watch(
	() => form.value.codice_fiscale,
	codice_fiscale => (form.value.codice_fiscale = codice_fiscale ? (codice_fiscale as string).toUpperCase() : '')
);

watch(
	() => form.value.fname,
	(fname: string) => {
		updateField('fname', fname);
	}
);

watch(
	() => form.value.lname,
	(lname: string) => {
		updateField('lname', lname);
	}
);
</script>

<template>
	<div class="grid md:grid-cols-2 md:gap-6">
		<div class="z-0 w-full mb-6 group">
			<!-- FNAME -->

			<AppInputElement
				v-model.trim="form.fname"
				label="Nome"
				:required="true"
			/>
		</div>

		<!-- LNAME -->
		<div class="z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.lname"
				label="Cognome"
				:required="true"
			/>
		</div>
	</div>

	<!-- PHONE -->
	<div class="grid md:grid-cols-2 md:gap-6">
		<div class="z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.phone"
				label="Telefono"
				type="tel"
			/>
		</div>

		<!-- EMAIL -->
		<div class="z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.email"
				label="Email"
				type="email"
			/>
		</div>
	</div>

	<div class="grid md:grid-cols-6 md:gap-6">
		<!-- BIRTHDAY -->

		<div class="md:col-span-2 z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.birthday"
				label="Data di nascita"
				type="date"
				:required="true"
			/>
		</div>

		<!-- BIRTHPLACE -->
		<div class="md:col-span-3 z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.birthplace"
				label="Nato a"
			/>
		</div>

		<!-- SEX -->
		<div class="col-span-1 z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.sex"
				label="Sesso"
				type="select"
			>
				<option>M</option>
				<option>F</option>
				<option value="O">Altro</option>
			</AppInputElement>
		</div>
	</div>

	<div class="grid md:grid-cols-3 gap-2 md:gap-6">
		<!-- BEGIN -->

		<div
			class="md:col-span-1 z-0 w-full mb-6 group"
			v-if="!isTest"
		>
			<AppInputElement
				v-model.trim="form.begin"
				label="Data di inizio terapia"
				type="date"
			/>
		</div>
		<!-- ADDRESS -->

		<div
			class="z-0 w-full mb-6 group"
			:class="isTest ? 'md:col-span-3' : 'md:col-span-2'"
		>
			<AppInputElement
				v-model.trim="form.address"
				label="Indirizzo"
			/>
		</div>
	</div>

	<div class="grid md:grid-cols-2 md:gap-6">
		<div class="z-0 w-full mb-6 group">
			<!-- CODICE FISCALE -->

			<AppInputElement
				v-model.trim="form.codice_fiscale"
				label="Codice Fiscale"
				min="16"
				max="16"
			/>
		</div>
		<!-- JOB -->

		<div class="z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.job"
				label="Lavoro"
			/>
		</div>
	</div>

	<div class="grid md:grid-cols-4 md:gap-6">
		<!-- WEIGHT -->

		<div class="md:col-span-1 z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.weight"
				label="Peso"
			/>
		</div>

		<!-- HEIGHT -->
		<div class="md:col-span-1 z-0 w-full mb-6 group">
			<AppInputElement
				v-model.trim="form.height"
				label="Altezza"
			/>
		</div>
		<div class="md:col-span-2 z-0 w-full mb-6 group">
			<!-- QUALIFICATION -->
			<AppInputElement
				v-model.trim="form.qualification"
				label="Titolo di Studio"
			/>
		</div>
	</div>
	<div class="md:col-span-2 z-0 w-full mb-6 group">
		<!-- COHABITANTS -->
		<AppInputElement
			v-model.trim="form.cohabitants"
			label="Conviventi"
		/>
	</div>

	<div>
		<button
			v-if="!isTest"
			type="button"
			class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto me-4"
			@click="emptyFields"
			ref="cancelButtonRef"
		>
			Svuota
		</button>
	</div>
</template>
