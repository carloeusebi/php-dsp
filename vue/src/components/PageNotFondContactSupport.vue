<script lang="ts" setup>
import { ref } from 'vue';
import AppModal from './AppModal.vue';
import AppButton from './AppButton.vue';
import AppInputElement from './AppInputElement.vue';
import axiosInstance from '@/assets/axios';
import { useLoaderStore } from '@/stores';
import { isAxiosError } from 'axios';

const showModal = ref(false);
const name = ref('');
const email = ref('');
const issue = ref('');

const emit = defineEmits(['contact-support', 'error']);

const handleFormSubmitted = async () => {
	if (!name.value || !issue.value) {
		return;
	}

	const loader = useLoaderStore();
	const data = {
		name: name.value,
		email: email.value,
		issue: issue.value,
	};
	loader.setLoader();

	try {
		await axiosInstance.post('/email/support', data);
		name.value = '';
		email.value = '';
		issue.value = '';
		emit('contact-support');
	} catch (err) {
		if (isAxiosError(err)) {
			const errors = err.response?.data;
			console.warn(errors);
			emit('error', errors);
		}
	} finally {
		loader.unsetLoader();
	}

	// hides modal and clear all fields
	showModal.value = false;
};
</script>

<template>
	<button
		@click="showModal = true"
		class="text-sm font-semibold text-gray-900"
	>
		Contatta il supporto <span aria-hidden="true">&rarr;</span>
	</button>

	<AppModal
		:open="showModal"
		@close="showModal = false"
		dimensions="max-w-screen-sm"
	>
		<template #content>
			<h2>Contatta il supporto</h2>
			<hr class="my-5" />
			<form
				@submit.prevent="handleFormSubmitted"
				id="contact-support"
			>
				<div class="grid md:grid-cols-2 md:gap-6">
					<AppInputElement
						v-model.trim="name"
						label="Nome"
						:required="true"
					/>
					<AppInputElement
						v-model.trim="email"
						label="Email"
						type="email"
					/>
				</div>
				<p class="my-6">Descrivi cosa stavi cercando quando ti sei trovato su questa pagina</p>
				<AppInputElement
					v-model="issue"
					label="Il problema riscontrato"
					type="textarea"
					:required="true"
				/>
			</form>
		</template>
		<template #button>
			<AppButton form="contact-support"> Invia </AppButton>
		</template>
	</AppModal>
</template>

<style scoped></style>
