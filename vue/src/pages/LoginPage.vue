<script lang="ts" setup>
import { Ref, ref } from 'vue';
import { useAuthStore, useLoaderStore } from '../stores';

import AppAlert from '../components/AppAlert.vue';
import { LoginForm } from '@/assets/data/interfaces';
import { isAxiosError } from 'axios';

const auth = useAuthStore();
const loader = useLoaderStore();

const form: Ref<LoginForm> = ref({
	username: '',
	password: '',
});
const isInvalid = ref(false);
const errorMessage = ref('');

const login = async () => {
	loader.setLoader();
	isInvalid.value = false;

	try {
		await auth.login(form.value);
	} catch (err) {
		isInvalid.value = true;
		if (isAxiosError(err)) {
			errorMessage.value =
				err.response?.status === 401
					? 'Username o Password errati'
					: err.response?.data['server-error'];
		} else console.warn(err);
	} finally {
		loader.unsetLoader();
		form.value.username = '';
		form.value.password = '';
	}
};
</script>

<template>
	<div
		class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8"
	>
		<div class="sm:mx-auto sm:w-full sm:max-w-sm">
			<!-- LOGO -->
			<img
				class="mx-auto h-max w-3/12 md:w-auto"
				src="/Favicon.png"
				alt="Della Santa Psicologo Logo"
			/>
			<h2
				class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
			>
				Accedi al tuo account
			</h2>
		</div>

		<div class="relative mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
			<!-- FORM -->
			<form
				class="space-y-6 mb-5"
				@submit.prevent="login"
				novalidate
			>
				<!-- EMAIL -->
				<div>
					<label
						for="email"
						class="block text-sm font-medium leading-6 text-gray-900"
						>Email
					</label>
					<div class="mt-2">
						<input
							v-model="form.username"
							id="email"
							type="email"
							autocomplete="email"
							required
							class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
						/>
					</div>
				</div>
				<!-- PASSWORD -->
				<div>
					<div class="flex items-center justify-between">
						<label
							for="password"
							class="block text-sm font-medium leading-6 text-gray-900"
							>Password</label
						>
					</div>
					<div class="mt-2">
						<input
							v-model="form.password"
							id="password"
							type="password"
							autocomplete="current-password"
							required
							class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
						/>
					</div>
				</div>

				<!-- BUTTON -->
				<div>
					<button
						type="submit"
						class="select-none flex w-full justify-center rounded-md bg-primary px-3 py-1.5 mb-4 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
					>
						Accedi
					</button>
					<a
						class="select-none flex w-full justify-center rounded-md border border-primary px-3 py-1.5 text-sm font-semibold leading-6 text-primary shadow-sm hover:bg-primary hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
						href="https://dellasantapsicologo.it"
						>Torna alla homepage</a
					>
				</div>
			</form>

			<!-- INVALID -->
			<div class="h-24">
				<AppAlert
					:show="isInvalid"
					:static="true"
					type="warning"
					title="Errore"
				>
					{{ errorMessage }}
				</AppAlert>
			</div>
		</div>
	</div>
</template>
