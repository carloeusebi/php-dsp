<script lang="ts" setup>
import { computed, ref } from 'vue';

import AppButton from '@/components/AppButton.vue';
import AppModal from '@/components/AppModal.vue';
import AppInputElement from '@/components/AppInputElement.vue';
import AppAlert from '@/components/AppAlert.vue';

import { Question } from '@/assets/data/interfaces';
import { useGetIndexOfFirstItemWithoutProp } from '@/composables';
import TestAnswer from './TestAnswer.vue';

interface Props {
	question: Question;
	current: number;
	total: number;
}
const props = defineProps<Props>();

const min = computed(() => (props.question.type === 'EDI' ? 0 : parseInt(props.question.type.at(0) as string)));
const max = computed(() => (props.question.type === 'EDI' ? 5 : parseInt(props.question.type.at(-1) as string)));

const firstNotAnsweredItemIndex = useGetIndexOfFirstItemWithoutProp(props.question.items, 'answer');
const active = ref(firstNotAnsweredItemIndex);

/**
 * Determines the css class that displays the the clicked answer
 */
const clicked = ref(-1);

const scroll = ref(false);
const showQuestionDescription = ref(true);
const showCommentModal = ref(false);
const comment = ref('');

const segments = computed(() => props.question.items.length);
const emit = defineEmits(['answered', 'question-complete']);

// keyboard click of valid num key triggers handleAnswer
window.addEventListener('keydown', e => {
	// ignores keydown event if comment modal is open
	if (showCommentModal.value) return;

	const keyPress = parseInt(e.key);
	if (isNaN(keyPress)) return;

	const { type, items } = props.question;
	const activeItemIndex = active.value;

	if (type === 'MUL') {
		// for MUL type Questionnaires
		const item = items[activeItemIndex];

		// Check if the active item allows multiple answers
		if (!item.multipleAnswers) return;

		// Check if the pressed key corresponds to a valid answer option
		if (keyPress - 1 < item.multipleAnswers.length && keyPress - 1 > item.multipleAnswers.length) return;

		// Retrieve the points associated with the selected multiple answer
		const answer = item.multipleAnswers[keyPress - 1].points;

		handleAnswer(answer);
	} else {
		//for other Questionnaire types
		const answer = keyPress - 1 + min.value;

		if (answer >= min.value && answer <= max.value) {
			handleAnswer(answer);
		}
	}
});

const activeItemId = computed(() => props.question.items[active.value].id);

/**
 * Goes to the next question and handles the animation
 */
const goToNextQuestion = () => {
	// scroll class triggers scroll animation
	scroll.value = true;

	// when animation end removes the scroll
	setTimeout(() => {
		scroll.value = false;
	}, 2500);

	setTimeout(() => {
		const isLast = () => active.value === props.question.items.length - 1;
		if (isLast()) {
			emit('question-complete');
			active.value = 0;
			showQuestionDescription.value = true;
			clicked.value = -1;
		} else {
			active.value++;
			comment.value = '';
			clicked.value = -1;
		}
	}, 1500);
};

/**
 * Handle the submission of an answer by the patient
 * @param answer The value of the selected answer
 */
const handleAnswer = (answer: number): void => {
	if (scroll.value) return;
	clicked.value = answer;

	emit('answered', activeItemId.value, answer);
	goToNextQuestion();
};

const addComment = () => {
	props.question.items[active.value].comment = comment.value;
	showCommentModal.value = false;
};

const showCommentAlert = ref(false);
/**
 * Allows the patient to skip the question, but he needs to add a comment explaining why
 */
const skipItem = () => {
	showCommentAlert.value = false;
	if (!comment.value) {
		showCommentAlert.value = true;
		return;
	}
	clicked.value = -1;
	addComment();
	emit('answered', activeItemId.value, -1);
	goToNextQuestion();
};
</script>

<template>
	<section class="flex flex-col max-w-lg mx-auto">
		<!-- QUESTION DESCRIPTION -->
		<div
			v-if="showQuestionDescription"
			class="relative z-10 pt-6 bg-gray-50"
		>
			<h2 class="text-center font-extrabold">{{ question.question }}</h2>
			<p class="text-justify">{{ question.description }}</p>
			<div class="flex justify-center mx-auto my-5">
				<AppButton
					color="green"
					@click="showQuestionDescription = false"
				>
					Rispondi
				</AppButton>
			</div>
		</div>

		<!-- PROGRESS BAR -->
		<div v-else>
			<div class="bg-gray-50 relative z-10 pt-3">
				<div class="text-center font-semibold">
					<div class="mb-2">{{ question.question }}</div>
					<div class="text-sm">Questionario {{ current }} di {{ total }}</div>
				</div>

				<div class="bg-gray-200 h-1 my-3 flex">
					<div
						class="h-1 flex-grow"
						v-for="segment in segments"
						:key="segment"
						:class="{ 'bg-secondary': segment <= active }"
					></div>
				</div>
			</div>
			<!-- ACTUAL QUESTION -->

			<div
				class="max-w-sm w-full mx-auto relative z-0"
				:class="{ scroll: scroll }"
			>
				<p class="item-description text-center text-lg font-semibold">
					{{ question.items[active].text }}
				</p>
				<!-- ! MUL -->
				<ul v-if="question.type === 'MUL'">
					<li
						v-for="(ans, n) in question.items[active].multipleAnswers"
						:key="ans.id"
						@click="handleAnswer(ans.points)"
						:class="{ active: clicked === ans.points }"
					>
						<TestAnswer
							:index="n + 1"
							:answer="ans.customAnswer"
						/>
					</li>
				</ul>
				<!-- ! OTHER -->
				<ul
					v-else
					class="my-5 w-full"
				>
					<li
						@click="handleAnswer(n + min)"
						v-for="(leg, n) in question.legend"
						:key="leg.id"
						:class="{ active: clicked === n + min }"
					>
						<TestAnswer
							:index="n + 1"
							:answer="leg.legend"
						/>
					</li>
				</ul>
				<!-- ADD COMMENT BUTTON -->
				<div class="flex justify-end">
					<button
						@click="showCommentModal = true"
						class="bg-primary hover:bg-secondary text-white w-full h-10 rounded-md font-bold"
					>
						<font-awesome-icon :icon="['far', 'comment-dots']" />
						Aggiungi un commento
					</button>
				</div>
			</div>
		</div>
	</section>

	<!-- ADD COMMENT MODAL -->

	<AppModal
		:open="showCommentModal"
		@close="showCommentModal = false"
		:disable-history-mode="true"
	>
		<template #content>
			<AppAlert
				:show="showCommentAlert"
				title="Errore"
				message="Se vuoi saltare la domanda devi scrivere un commento."
				type="warning"
			/>

			<h3 class="font-bold my-3">Scrivi qui il tuo commento:</h3>
			<form
				id="comment-form"
				@submit.prevent="addComment"
			>
				<AppInputElement
					type="textarea"
					v-model.trim="comment"
				/>
			</form>
		</template>
		<template #button>
			<AppButton form="comment-form"> Aggiungi commento</AppButton>
			<AppButton
				class="me-3"
				color="green"
				@click="skipItem"
			>
				Salta questa domanda</AppButton
			>
		</template>
	</AppModal>
</template>

<style lang="scss" scoped>
$primary-color: #6ecc84;
$secondary-color: #254d32;
.item-description {
	min-height: 75px;
}

li {
	position: relative;
	display: flex;
	align-items: center;
	min-height: 40px;
	width: 100%;

	margin-bottom: 8px;
	padding: 4px 0;
	border-radius: 4px;
	background-color: #{$primary-color}19;
	color: $secondary-color;
	box-shadow: $secondary-color 0 0 0 1px inset;

	cursor: pointer;
}

.active {
	animation: at-click 0.25s ease 0s 2 normal none running;

	box-shadow: $secondary-color 0 0 0 2px inset;

	:deep(.score) {
		border-color: $secondary-color;
		background-color: $secondary-color;
		color: white;
	}

	.fa-check {
		display: inline-block;
	}
}

@keyframes at-click {
	50% {
		opacity: 0.3;
	}
}

.scroll {
	animation: scroll 2s ease-in-out 1s 1 forwards;
}

@keyframes scroll {
	33% {
		transform: translateY(-100%);
		opacity: 0;
	}
	34% {
		transform: translateY(100%);
		opacity: 0;
	}
	66% {
		transform: translate(0);
		opacity: 1;
	}
}
</style>
