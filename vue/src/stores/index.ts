import { useQuestionsStore } from './_questions';
import { usePatientsStore } from './_patients';
import { useSurveysStore } from './_surveys';
import { useLoaderStore } from './_loader';
import { useTagsStore } from './_tags';
import { AxiosInstance } from 'axios';
import { useTestsStore } from './_tests';
import { useAuthStore } from './_auth';
import { createPinia } from 'pinia';
import { markRaw } from 'vue';
import { Router } from 'vue-router';
import router from '@/routes';
import axiosInstance from '@/assets/axios';

declare module 'pinia' {
	export interface PiniaCustomProperties {
		router: Router;
		axios: AxiosInstance;
	}
}

const pinia = createPinia();

pinia.use(({ store }) => {
	store.router = markRaw(router);
	store.axios = markRaw(axiosInstance);
});

export {
	pinia,
	useAuthStore,
	usePatientsStore,
	useLoaderStore,
	useSurveysStore,
	useQuestionsStore,
	useTestsStore,
	useTagsStore,
};
