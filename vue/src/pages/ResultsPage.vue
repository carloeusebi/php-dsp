<script lang="ts" setup>
import { Question, QuestionItem, Survey } from '@/assets/data/interfaces';
import { useLoaderStore, useSurveysStore } from '@/stores';
import axios from 'axios';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const id = route.params.id as string;

const editMode = ref(false);

const survey = useSurveysStore().getById(id) as Survey;

const min = (question: Question): number => {
	return parseInt(question.type.at(0) as string);
};

/**
 * Updates an answer
 * @param questionId the question where the answer to update is
 * @param itemId the item to update
 * @param answer the new answer
 */
const changeAnswer = (
	questionId: number,
	itemId: number,
	answer: number
): void => {
	// return if we are not in question mode
	if (!editMode.value) return;

	const questionToUpdate = survey?.questions.find(
		({ id }) => id === questionId
	);

	const itemToUpdate = questionToUpdate?.items.find(
		({ id }) => id === itemId
	) as QuestionItem;

	itemToUpdate.answer = answer;
};

/**
 * Saves the survey and uses the store to make an ajax call
 */
const saveUpdates = async () => {
	const loader = useLoaderStore();
	loader.setLoader();
	try {
		await useSurveysStore().save({ ...survey });
	} catch (err) {
		if (axios.isAxiosError(err)) console.warn(err.response?.data);
		else console.error(err);
	} finally {
		loader.unsetLoader();
		editMode.value = false;
	}
};
</script>

<template>
	<div class="bg-white min-h-screen">
		<div class="container max-w-6xl mx-auto p-6">
			<!-- HEADER -->
			<header class="mb-8">
				<figure class="flex justify-end">
					<img
						src="/Logo.webp"
						alt="Logo"
					/>
				</figure>
				<h1 class="md:text-3xl font-bold my-10">{{ survey?.title }}</h1>
			</header>

			<!-- PATIENT -->
			<section
				id="patient"
				class="border-b pb-2 mb-5"
			>
				<div>
					<h2 class="mb-3">Paziente:</h2>
					<strong>Nome e cognome: </strong>{{ survey?.fname }}
					{{ survey?.lname }}
				</div>
				<div>
					<span><strong>Età: </strong>{{ survey?.age }}</span>
					<span
						class="ms-3"
						v-if="survey?.weight"
						>| <strong>Peso: </strong>{{ survey?.weight }}kg</span
					>
					<span
						class="ms-3"
						v-if="survey?.height"
						>| <strong>Altezza: </strong>{{ survey?.height }}cm</span
					>
				</div>
				<div v-if="survey?.job">
					<strong>Professione: </strong>{{ survey?.job }}
				</div>
				<div v-if="survey?.cohabitants">
					<strong>Vive con: </strong>{{ survey?.cohabitants }}
				</div>
			</section>

			<!-- QUESTIONNAIRE -->
			<section
				v-for="question in survey?.questions"
				:key="question.id"
				:id="(question.id as number).toString()"
				class="my-10 border-b pb-5"
				:class="{ 'edit-mode': editMode }"
			>
				<h2>{{ question.question }}</h2>
				<p>{{ question.description }}</p>
				<!-- LEGEND -->
				<div
					class="border border-black my-5 p-2 grid md:grid-cols-2 md:text-xl"
				>
					<div
						v-for="(legend, i) in question.legend"
						:key="i"
						class="uppercase font-bold"
					>
						{{ i + min(question) }} = {{ legend.legend }}
					</div>
				</div>
				<!-- QUESTIONS -->
				<div>
					<div
						v-for="item in question.items"
						:key="item.id"
						class="grid grid-cols-6 relative"
					>
						<div
							class="col-span-4 p-2 border border-black flex justify-between"
						>
							{{ item.text }}
						</div>
						<!-- ANSWERS -->
						<div class="col-span-2 flex">
							<!-- @vue-ignore -->
							<div
								v-for="(leg, n) in question.legend"
								:key="n"
								class="answer-cell border border-black flex-grow flex justify-center items-center"
								:class="{ 'bg-green-500': n + min(question) === item.answer }"
								@click="
									changeAnswer(
										question.id as number,
										item.id,
										n + min(question)
									)
								"
							>
								{{ n + min(question) }}
							</div>
						</div>
						<!-- COMMENTS -->
						<div
							class="comment-container"
							v-if="item.comment"
						>
							<font-awesome-icon
								:icon="['far', 'comment-dots']"
								size="xl"
								class="z-0"
							/>
							<div class="comment">
								{{ item.comment }}
							</div>
						</div>
						<div
							v-if="item.comment"
							class="print-comment"
						>
							*{{ item.comment }}
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>

	<!-- EDIT BUTTON -->
	<div
		class="edit-button"
		:class="[
			editMode
				? 'bg-red-800 hover:bg-red-900'
				: 'bg-blue-800 hover:bg-blue-900',
		]"
		@click="editMode = !editMode"
	>
		<font-awesome-icon
			:icon="['fas', 'pen-to-square']"
			size="2xl"
		/>
	</div>

	<!-- SAVE BUTTON -->
	<div
		v-if="editMode"
		@click="saveUpdates"
		class="save-button bg-green-800 hover:bg-green-900"
	>
		<font-awesome-icon
			:icon="['fas', 'floppy-disk']"
			size="2xl"
		/>
	</div>

	<!-- EDIT ALERT -->
	<div
		v-if="editMode"
		class="edit-mode-alert text-center text-xl md:text-4xl text-red-600 font-extrabold top-12 md:top-32 w-auto uppercase"
	>
		Modalità modifica attiva
	</div>
</template>

<style lang="scss" scoped>
* {
	-webkit-print-color-adjust: exact;
	print-color-adjust: exact;
}

img {
	max-width: 50%;
	width: 350px;
}

.comment-container {
	position: absolute;
	top: 50%;
	left: 100.5%;
	transform: translateY(-50%);
	cursor: pointer;
	z-index: 1;

	&:hover .comment {
		display: flex;
	}
}

.comment {
	position: absolute;
	justify-content: center;
	align-items: center;
	display: none;
	background-color: #fff;
	padding: 2rem;
	box-shadow: 0 0 10px 2px black;
	z-index: 10;
	border-radius: 20px;
	right: 5px;
	width: 800px;
	max-width: 75vw;
}

.print-comment {
	display: none;
	text-align: right;
	width: 700px;
}

.edit-button,
.save-button {
	position: fixed;
	left: 2rem;

	box-shadow: 0 0 20px 2px #888;
	color: white;
	border-radius: 50%;
	width: 75px;
	aspect-ratio: 1;

	display: flex;
	justify-content: center;
	align-items: center;

	cursor: pointer;
}

.edit-button {
	bottom: 2rem;
}

.save-button {
	bottom: 7.5rem;
}

.edit-mode-alert {
	position: fixed;
	left: 50%;
	transform: translateX(-50%);

	text-shadow: 0 0 10px #888;
	user-select: none;
}

.edit-mode .answer-cell {
	cursor: pointer;
}

// PRINT MEDIA

@media print {
	.print-comment {
		display: block;
	}

	.fa-comment-dots {
		display: none;
	}

	.edit-button {
		display: none;
	}
}
</style>
