import { defineStore } from 'pinia';
import { usePatientsStore, useSurveysStore, useQuestionsStore, useTagsStore } from './index';
import { LoginForm } from '@/assets/data/interfaces';
import { isAxiosError } from 'axios';

export const useAuthStore = defineStore('auth', {
	//state
	state: () => ({
		user: JSON.parse(localStorage.getItem('USER') as string) as string | null,
	}),

	//getters
	getters: {
		isAdmin: state => state.user === 'ADMIN',
		isGuest: state => !state.user,
	},

	// actions
	actions: {
		/**
		 * Fills all the Admin stores
		 */
		fetchAllData() {
			useSurveysStore().fetch();
			usePatientsStore().fetch();
			useQuestionsStore().fetch();
			useTagsStore().fetch();
		},

		/**
		 * Calls the login api, if successful saves the answers in the store, otherwise return an error
		 * @param credentials The credentials for the login
		 * @returns
		 */
		async login(credentials: LoginForm) {
			return this.axios
				.post('/login', credentials)
				.then(({ data }: { data: LoginResponseData }) => {
					const user = data.user;
					this.user = user;

					localStorage.setItem('USER', JSON.stringify(user));

					this.fetchAllData();

					this.router.push('/');
				})
				.catch(err => {
					throw err;
				});
		},

		/**
		 * Calls the validate api and validates that the Admin session is still open backend-side
		 */
		async validate() {
			return this.axios.get('/validate').catch(e => {
				if (isAxiosError(e)) {
					console.warn(e.response?.data);
					throw e;
				}
			});
		},

		/**
		 * logout
		 */
		async logout() {
			return this.axios
				.delete('/logout')
				.then(() => {
					this.removeUser();
				})
				.catch(err => {
					throw err;
				});
		},

		/**
		 * removes the user
		 */
		removeUser() {
			localStorage.clear();
			this.user = null;
			this.router.push('/login');
		},
	},
});

interface LoginResponseData {
	user: string;
	session_id: string;
}
