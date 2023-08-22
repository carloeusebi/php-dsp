import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores';
import { routes } from './routes';

const router = createRouter({
	history: createWebHistory('/admin/'),
	routes,
	linkActiveClass: 'text-gray-900 font-medium font-black',
});

// MIDDLEWARES

router.beforeEach((to, _from, next) => {
	const user = useAuthStore();

	if (to.meta.title) {
		window.document.title = to.meta.title as string;
	}

	// ADMIN;
	if (to.meta.requiresAdmin) {
		if (user.isAdmin) next();
		else next({ name: 'login' });
	}

	// GUEST;
	else if (to.meta.requiresGuest) {
		if (user.isAdmin) next({ name: 'admin' });
		else next();
	}

	// ELSE;
	else next();
});

export default router;
