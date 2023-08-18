<script lang="ts" setup>
import { useAuthStore, useLoaderStore } from '@/stores';
import AppButtonBlank from './AppButtonBlank.vue';
import { isAxiosError } from 'axios';
const auth = useAuthStore();

const handleLogout = async () => {
	const loader = useLoaderStore();
	loader.setLoader();
	try {
		await auth.logout();
	} catch (err) {
		if (isAxiosError(err)) console.warn(err.response?.data);
		else console.error(err);
	} finally {
		loader.unsetLoader();
	}
};
</script>

<template>
	<AppButtonBlank
		@click="handleLogout"
		color="red"
		label="Esci"
	/>
</template>
