import AdminLayout from '@/layouts/AdminLayout.vue';
import LoginPage from '@/pages/LoginPage.vue';
import PatientsPage from '@/pages/PatientsPage.vue';
import SurveysPage from '@/pages/SurveysPage.vue';
import QuestionsPage from '@/pages/QuestionsPage.vue';
import TestPage from '@/pages/TestPage.vue';
import ResultsPage from '@/pages/ResultsPage.vue';
import PageNotFound from '@/pages/PageNotFound.vue';
import ScoresPage from '@/pages/ScoresPage.vue';

export const routes = [
	{
		path: '/',
		component: AdminLayout,
		name: 'admin',
		redirect: '/pazienti',
		meta: {
			requiresAdmin: true,
		},
		children: [
			{
				path: '/pazienti',
				component: PatientsPage,
				name: 'patients',
			},
			{
				path: '/sondaggi',
				component: SurveysPage,
				name: 'surveys',
			},
			{
				path: '/questionari',
				component: QuestionsPage,
				name: 'questions',
			},
		],
	},
	{
		path: '/login',
		component: LoginPage,
		name: 'login',
		meta: {
			requiresGuest: true,
		},
	},
	{
		path: '/test',
		component: TestPage,
		name: 'test',
		meta: {
			title: 'Dellasanta Psicologo | Test',
		},
	},
	{
		path: '/risposte/:id',
		component: ResultsPage,
		name: 'results',
		meta: {
			title: 'Risposte Test',
		},
	},
	{
		path: '/punteggi/:id',
		component: ScoresPage,
		name: 'scores',
		meta: {
			title: 'Risultati Test',
		},
	},
	{
		path: '/:pathMatch(.*)*',
		component: PageNotFound,
		name: '404',
	},
];
