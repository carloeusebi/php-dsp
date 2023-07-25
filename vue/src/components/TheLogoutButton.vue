<script lang="ts" setup>
import { useAuthStore } from '@/stores';
import AppButtonBlank from './AppButtonBlank.vue';
const auth = useAuthStore();
</script>

<template>
	<AppButtonBlank
		@click="auth.logout()"
		color="red">
		Esci
	</AppButtonBlank>
</template>
