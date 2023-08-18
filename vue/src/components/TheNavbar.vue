<script lang="ts" setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';
import TheLogoutButton from './TheLogoutButton.vue';

const navigation = [
	{ name: 'Pazienti', to: 'patients' },
	{ name: 'Sondaggi', to: 'surveys' },
];
</script>

<template>
	<Disclosure
		as="nav"
		class="bg-white drop-shadow-md sticky top-0 left-0 right-0 z-20"
		v-slot="{ open }"
	>
		<div class="mx-auto max-w-screen-2xl px-2 sm:px-6 lg:px-8">
			<div class="relative flex h-20 items-center justify-between">
				<!-- Mobile menu button-->
				<div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
					<DisclosureButton
						class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
					>
						<Bars3Icon
							v-if="!open"
							class="block h-6 w-6"
						/>
						<XMarkIcon
							v-else
							class="block h-6 w-6"
						/>
					</DisclosureButton>
				</div>

				<div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
					<div class="flex flex-shrink-0 items-center">
						<img
							class="h-10 lg:h-14 w-auto"
							src="/Logo.webp"
							alt="Logo"
						/>
					</div>

					<div class="hidden sm:ml-6 sm:flex items-center">
						<div class="flex space-x-4">
							<router-link
								v-for="item in navigation"
								:key="item.name"
								class="text-gray-500 rounded-md px-3 py-2 text-md cursor-pointer"
								:to="{ name: item.to }"
							>
								{{ item.name }}
							</router-link>
						</div>
					</div>
				</div>

				<!-- LOGOUT -->
				<TheLogoutButton />
			</div>
		</div>

		<!-- DROPDOWN -->

		<DisclosurePanel class="sm:hidden">
			<div class="space-y-1 px-2 pb-3 pt-2">
				<router-link
					v-for="item in navigation"
					:key="item.name"
					class="text-gray-500 rounded-md px-3 py-2 text-md font-medium cursor-pointer"
					:to="{ name: item.to }"
				>
					{{ item.name }}
				</router-link>
			</div>
		</DisclosurePanel>
	</Disclosure>
</template>
