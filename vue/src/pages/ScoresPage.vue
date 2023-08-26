<script lang="ts" setup>
import { useRoute } from 'vue-router';
import { ref, onMounted } from 'vue';
import { isAxiosError } from 'axios';

import ResultsHeader from '@/components/ResultsHeader.vue';
import ResultsPatient from '@/components/ResultsPatient.vue';

import { useLoaderStore } from '@/stores';
import axiosInstance from '@/assets/axios';
import { Question, QuestionVariableCutoff, Survey } from '@/assets/data/interfaces';

interface Scores {
	[string: string]: { [string: string]: number };
}

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
		if (isAxiosError(err) && err.response?.status === 403)
			alert("Devi aver effettuato l'accesso per vedere questa pagina");
		else alert(err);
	} finally {
		loader.unsetLoader();
	}
});

const score = (question: Question['question'], variable: string): number => {
	return scores.value ? scores.value[question][variable] : 0;
};

const printCutoff = (cutoff: QuestionVariableCutoff): string => {
	let elementToPrint = '';
	const { type } = cutoff;

	if (type === 'greater-than') elementToPrint = `> ${cutoff.from}`;
	else if (type === 'lesser-than') elementToPrint = `< ${cutoff.from}`;
	else elementToPrint = `${cutoff.from} - ${cutoff.to}`;

	return elementToPrint;
};

const scored = (score: number, cutoff: QuestionVariableCutoff): boolean => {
	const { type } = cutoff;

	if (type === 'greater-than') {
		return score > cutoff.from;
	} else if (type === 'lesser-than') {
		return score < cutoff.from;
	} else {
		return score >= cutoff.from && score <= cutoff.to;
	}
};
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

			<!-- RESULTS -->
			<div v-if="scores">
				<section
					v-for="question in survey?.questions"
					:key="question.id"
					:id="question.id.toString()"
				>
					<h2>
						{{ question.question }}
					</h2>

					<!-- VARIABLES -->
					<table class="table-auto mb-8">
						<thead>
							<tr class="text-left">
								<th class="md:min-w-[300px]">Variabile</th>
								<th>Punteggio</th>
								<th>Cutoffs</th>
								<th>Indicazioni</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="variable in question.variables"
								:key="variable.id"
								class="align-top"
							>
								<!-- variable name -->
								<td>{{ variable.name }}</td>
								<!-- score -->
								<td class="text-end">{{ score(question.question, variable.name) }}</td>
								<!-- cutoffs -->
								<td>
									<ul>
										<li
											v-for="cutoff in variable.cutoffs"
											:key="cutoff.id"
										>
											{{ printCutoff(cutoff) }}
										</li>
									</ul>
								</td>
								<!-- indications -->
								<td>
									<ul>
										<li
											v-for="cutoff in variable.cutoffs"
											:key="cutoff.id"
											class="px-4"
											:class="{ 'bg-yellow-200': scored(score(question.question, variable.name), cutoff) }"
										>
											{{ cutoff.name }}
										</li>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
					<hr class="mb-8" />
				</section>
			</div>
		</div>
	</div>
</template>

<style scoped>
table th,
table td {
	padding: 0 1rem;
}
</style>
