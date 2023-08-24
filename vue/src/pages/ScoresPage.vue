<script lang="ts" setup>
import { useRoute } from 'vue-router';
import { ref, onMounted } from 'vue';
import { isAxiosError } from 'axios';

import ResultsHeader from '@/components/ResultsHeader.vue';
import ResultsPatient from '@/components/ResultsPatient.vue';

import { useLoaderStore } from '@/stores';
import axiosInstance from '@/assets/axios';
import { Survey } from '@/assets/data/interfaces';

interface Scores {}

const id = useRoute().params.id;
const loader = useLoaderStore();
const survey = ref<Survey | null>(null);
const scores = ref<Scores | null>(null);

/**
 * On mount fetches all survey details and the survey scores
 */
onMounted(async () => {
	loader.setLoader();
	const params = { id };
	try {
		const res = await axiosInstance.get('surveys/score', { params });
		survey.value = res.data.survey;
		scores.value = res.data.scores;
	} catch (err) {
		if (isAxiosError(err)) alert(err.response?.data);
		else alert(err);
	} finally {
		loader.unsetLoader();
	}
});
</script>

<template>
	<div class="bg-white min-h-screen">
		<div class="container max-w-6xl mx-auto p-6">
			<!-- HEADER -->
			<ResultsHeader :title="survey?.title" />

			<!-- PATIENT -->
			<ResultsPatient
				v-if="survey"
				:survey="survey"
			/>
		</div>
	</div>
</template>

<style scoped></style>
