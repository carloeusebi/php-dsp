<script lang="ts" setup>
import { useSurveysStore, usePatientsStore } from '@/stores';
import { storeToRefs } from 'pinia';
import { Ref, computed, ref } from 'vue';
import { useSearchFilter, useSort } from '@/composables';

import AppSearchbar from '@/components/AppSearchbar.vue';
import AppAlert from '@/components/AppAlert.vue';
import AppTable from '../components/AppTable.vue';
import SurveyRow from '../components/SurveyRow.vue';
import { Cell, Order, Patient, Survey } from '@/assets/data/interfaces';
import AppButtonBlank from '@/components/AppButtonBlank.vue';
import SurveyCreate from '@/components/SurveyCreate.vue';

const surveysStore = useSurveysStore();
const { surveys } = storeToRefs(surveysStore);

// REFS
const searchWord = ref('');

export interface SurveyCell extends Cell {
	key: keyof Survey;
}

interface OrderSurvey extends Order {
	by: keyof Survey;
}

const tableCells: Ref<SurveyCell[]> = ref([
	{ label: 'Paziente', key: 'patient_name' },
	{ label: 'Titolo', key: 'title' },
	{ label: 'Creato il', key: 'created_at' },
	{ label: 'Ultima modifica', key: 'last_update' },
	{ label: 'Completato', key: 'completed' },
]);

const handleSearchbarKeypress = (word: string) => {
	searchWord.value = word.toLowerCase();
};

const filteredBySearchSurveys = computed(() => {
	if (surveys.value === null) return [];
	return useSearchFilter(surveys.value, searchWord.value, [
		'title',
		'patient_name',
	]);
});

const sort = (newOrder: Order) => {
	order.value = { ...newOrder } as OrderSurvey;
};

const order: Ref<OrderSurvey> = ref({ by: 'id', type: 'down' });

const filteredAndOrderedSurveys = computed(() => {
	if (filteredBySearchSurveys.value === null) return [];
	return useSort(
		filteredBySearchSurveys.value,
		order.value.by,
		order.value.type
	);
});
</script>

<template>
	<section class="relative container mx-auto mt-6 p-2 lg:p-6">
		<!-- SEARCH -->
		<div class="relative flex justify-between w-full">
			<AppSearchbar @key-press="handleSearchbarKeypress" />
		</div>

		<div class="flex justify-end my-3">
			<!-- CREATE BUTTON -->
			<SurveyCreate />
			<!-- QUESTIONS BUTTON -->
			<router-link to="/questionari">
				<AppButtonBlank>
					<font-awesome-icon :icon="['fas', 'pen']" />
					<span class="hidden md:inline ms-3">Modifica questionari</span>
				</AppButtonBlank>
			</router-link>
		</div>

		<!-- TABLE -->

		<AppTable
			v-if="filteredAndOrderedSurveys.length > 0"
			@sort-change="sort"
			:cells="tableCells"
			:has-reset="true"
		>
			<template v-slot:tbody>
				<SurveyRow
					v-for="survey in filteredAndOrderedSurveys"
					:survey="survey"
					:cells="tableCells"
					:key="survey.id || survey.title"
				/>
			</template>
		</AppTable>
		<div v-else>
			<AppAlert
				:show="true"
				title="Ops!"
			>
				Nessun sondaggio trovato!
			</AppAlert>
		</div>
	</section>
</template>

<style scoped>
/* min width to prevent dates to break line */
:deep(tr :is(td:nth-child(3), td:nth-child(4))) {
	min-width: 120px;
}
</style>
