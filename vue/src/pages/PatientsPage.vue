<script lang="ts" setup>
import { ref, computed, Ref } from 'vue';
import { usePatientsStore } from '@/stores';
import { storeToRefs } from 'pinia';
import { useSort, useSearchFilter } from '@/composables';

import PatientRow from '@/components/PatientRow.vue';
import AppAlert from '@/components/AppAlert.vue';
import PatientSave from '@/components/PatientSave.vue';
import AppSearchbar from '@/components/AppSearchbar.vue';
import AppTable from '../components/AppTable.vue';
import { Cell, Order, Patient } from '@/assets/data/interfaces';

const patientsStore = usePatientsStore();
const { patients } = storeToRefs(patientsStore);

// REFS

const searchWord = ref('');

export interface PatientCell extends Cell {
	key: keyof Patient;
}

interface OrderPatient extends Order {
	by: keyof Patient;
}

const tableCells: Ref<PatientCell[]> = ref([
	{ label: 'Nome', key: 'fname' },
	{ label: 'Cognome', key: 'lname' },
	{ label: 'Età', key: 'age' },
	{ label: 'Email', key: 'email' },
	{
		label: `<div><span class="hidden md:inline">Data </span>Inizio<span class="hidden xl:inline"> Terapia</span></div>`,
		key: 'begin',
	},
]);

// FILTER BY SEARCH

const handleSearchbarKeypress = (word: string) => (searchWord.value = word.toLowerCase());

const filteredBySearchPatients = computed(() => {
	if (patients.value === null) return [];
	return useSearchFilter(patients.value, searchWord.value, ['fname', 'lname']);
});

// SORT

const sort = (newOrder: Order) => {
	order.value = { ...newOrder } as OrderPatient;
};

const order: Ref<OrderPatient> = ref({ by: 'id', type: 'down' });

const filteredAndOrderedPatients = computed(() =>
	useSort(filteredBySearchPatients.value, order.value.by, order.value.type)
);
</script>

<template>
	<section class="relative container mx-auto mt-6 p-2 lg:p-6">
		<!-- SEARCH -->
		<div class="relative flex justify-between w-full mb-5">
			<AppSearchbar @key-press="handleSearchbarKeypress" />
			<PatientSave
				icon="plus"
				title="Aggiungi un paziente"
				button-label="Aggiungi" />
		</div>

		<!-- TABLE -->
		<AppTable
			v-if="filteredAndOrderedPatients.length > 0"
			@sort-change="sort"
			:cells="tableCells"
			:has-reset="true">
			<template v-slot:tbody>
				<PatientRow
					v-for="patient in filteredAndOrderedPatients"
					:cells="tableCells"
					:patient="patient"
					:key="patient.id || patient.lname + patient.fname" />
			</template>
		</AppTable>
		<div v-else>
			<AppAlert
				:show="true"
				title="Ops!">
				Nessun paziente trovato!
			</AppAlert>
		</div>
	</section>
</template>

<style scoped>
:deep(tr td:nth-child(5)) {
	min-width: 120px;
}
</style>
