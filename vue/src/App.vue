<script lang="ts" setup>
import AppLoader from './components/AppLoader.vue';

import { useAuthStore } from './stores';

const user = useAuthStore();

// validate admin at avery page load
if (user.isAdmin) {
	user
		.validate()
		.then(() => {
			user.fetchAllData();
		})
		.catch();
}
</script>

<template>
	<router-view></router-view>
	<AppLoader />
</template>
