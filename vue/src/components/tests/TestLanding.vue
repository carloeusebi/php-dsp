<script lang="ts" setup>
import AppButton from '@/components/AppButton.vue';

const emit = defineEmits(['start']);
</script>

<template>
	<section
		id="landing-page"
		class="py-5 md:max-w-5xl mx-auto"
	>
		<h1 class="text-center text-4xl font-bold uppercase mb-5">QUESTIONARI PER LA VALUTAZIONE PSICOLOGICA</h1>
		<p class="text-justify">
			Questo fascicolo contiene una serie di questionari appositamente selezionati per esplorare alcuni aspetti del
			funzionamento psicologico che potrebbero influire sul Suo benessere. La preghiamo di compilare ogni questionario
			in tutte le sue parti, anche nel caso in cui qualche domanda possa risultarle poco chiara o non pertinente. La
			compilazione di tutto il plico di questionari richiede circa un'ora di tempo, quindi, nel caso si dovesse sentire
			particolarmente affaticata/o, può effettuare una o più interruzioni. Da ogni questionario, completato per intero,
			verrà ricavato un punteggio che darà delle indicazioni sul suo stato. Questi dati permetteranno di integrare
			quanto emerso nei colloqui con informazioni che potranno servire per impostare un trattamento più adeguato al suo
			problema e alla sua personalità. Il terapeuta con il quale è in contatto fara in modo di comunicarle il profilo
			risultante.
		</p>
		<div class="flex justify-center my-6">
			<AppButton
				color="green"
				class="px-10"
				@click="emit('start')"
			>
				Inizia
			</AppButton>
		</div>
	</section>
</template>

<style scoped></style>
