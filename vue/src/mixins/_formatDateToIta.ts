/**
 * Formats a given date in italian date format
 * @param date The date and time (e.g., "2023-08-25T12:34:56").
 * @returns The date formatted (e.g., "2023-25-08")
 */
export function formatDateToIta(date: string): string {
	const d = new Date(date);

	const day = d.getDate() < 10 ? `0${d.getDate()}` : d.getDate();
	const month = d.getMonth() < 10 ? `0${d.getMonth()}` : d.getMonth();

	return `${d.getFullYear()}-${day}-${month}`;
}
