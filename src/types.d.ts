declare module '*.scss';

declare module '*.jpg' {
	const imageUrl: string;
	export default imageUrl;
}

interface Window {
	avclProductPriceCurrencies: Record<
		string,
		{ label: string; symbol: string }
	>;
}
