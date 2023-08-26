<script lang="ts" setup>
import { Survey } from '@/assets/data/interfaces';

interface Props {
	survey: Survey;
}

defineProps<Props>();
</script>

<template>
	<section
		id="patient"
		class="border-b pb-2 mb-5"
	>
		<div>
			<h2 class="mb-3">Paziente:</h2>
			<strong>Nome e cognome: </strong>{{ survey?.fname }}
			{{ survey?.lname }}
		</div>
		<div>
			<span><strong>Età: </strong>{{ survey?.age }}</span>
			<span
				class="ms-3"
				v-if="survey?.weight"
				>| <strong>Peso: </strong>{{ survey?.weight }}kg</span
			>
			<span
				class="ms-3"
				v-if="survey?.height"
				>| <strong>Altezza: </strong>{{ survey?.height }}cm</span
			>
		</div>
		<div v-if="survey?.job"><strong>Professione: </strong>{{ survey?.job }}</div>
		<div v-if="survey?.cohabitants"><strong>Vive con: </strong>{{ survey?.cohabitants }}</div>
	</section>
</template>

<style scoped></style>
