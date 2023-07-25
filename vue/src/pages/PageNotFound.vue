<script lang="ts" setup>
import AppAlert from '@/components/AppAlert.vue';
import PageNotFondContactSupport from '@/components/PageNotFondContactSupport.vue';
import { Ref, ref } from 'vue';

interface Errors {
	[string: string]: string;
}

const alertShow = ref(false);
const alertTitle = ref('');
const alertMessage = ref('');
const alertType: Ref<'warning' | 'success' | 'info'> = ref('info');

const handleEmailSent = () => {
	alertShow.value = true;
	alertType.value = 'success';
	alertTitle.value = 'Email Inviata';
	alertMessage.value = 'Grazie per aver contattato il supporto';
};

const handleError = (errors: Errors) => {
	alertShow.value = true;
	alertType.value = 'warning';
	alertTitle.value = "E' stato riscontrato un problema";

	const keys = Object.keys(errors);
	const errorMsg = keys.reduce((str, key) => (str += `${errors[key]}<br>`), '');

	alertMessage.value = errorMsg;
};
</script>

<template>
	<main
		class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8"
	>
		<div class="text-center self-end">
			<p class="text-base font-semibold text-primary">404</p>
			<h1
				class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl"
			>
				Pagina non trovata!
			</h1>
			<p class="mt-6 text-base leading-7 text-gray-600">
				Spiacenti, non siamo riusciti a trovare quello che cercavi.
			</p>
			<div class="mt-10 flex items-center justify-center gap-x-6">
				<a
					href="https://dellasantapsicologo.it"
					class="rounded-md bg-primary px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-secondary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
					>Torna alla homepage</a
				>
				<PageNotFondContactSupport
					@contact-support="handleEmailSent"
					@error="handleError"
				/>
			</div>
		</div>
		<div class="h-24 min-w-[40%]">
			<AppAlert
				:show="alertShow"
				:title="alertTitle"
				:type="alertType"
			>
				<span v-html="alertMessage"></span>
			</AppAlert>
		</div>
	</main>
</template>
