<x-filament-widgets::widget>
    <style>
        .bg-clip-text {
            -webkit-background-clip: text;
            background-clip: text;
        }

        .gradient-text {
            background-image: linear-gradient(to right, #3b82f6, #a855f7, #ec4899);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .7;
            }
        }
    </style>

    <div
        x-data="{
            quote: '',
            character: '',
            displayedQuote: '',
            displayedCharacter: '',
            fullQuote: '',
            fullCharacter: '',

            async fetchQuote() {
                try {
                    const randomPage = Math.floor(Math.random() * 34) + 1;
                    const response = await fetch(`https://katanime.vercel.app/api/getbyanime?anime=naruto&page=${randomPage}`);
                    const data = await response.json();

                    if (data.result && data.result.length > 0) {
                        const randomIndex = Math.floor(Math.random() * data.result.length);
                        this.fullQuote = data.result[randomIndex].indo;
                        this.fullCharacter = `- ${data.result[randomIndex].character}`;
                        this.typeQuote();
                    } else {
                        this.quote = 'Tidak ada kutipan ditemukan';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.quote = 'Gagal mengambil kutipan';
                }
            },

            async typeQuote() {
                this.displayedQuote = '';
                this.displayedCharacter = '';

                // Mengetik quote
                for (let i = 0; i < this.fullQuote.length; i++) {
                    await new Promise(resolve => setTimeout(resolve, 50));
                    this.displayedQuote += this.fullQuote[i];
                }

                // Jeda sebelum mengetik karakter
                await new Promise(resolve => setTimeout(resolve, 500));

                // Mengetik karakter
                for (let i = 0; i < this.fullCharacter.length; i++) {
                    await new Promise(resolve => setTimeout(resolve, 50));
                    this.displayedCharacter += this.fullCharacter[i];
                }
            }
        }"
        x-init="fetchQuote()"
        class="p-4"
    >
        <h1 x-text="displayedQuote" class="py-4 text-2xl font-bold gradient-text"></h1>
        <h3 x-text="displayedCharacter" class="py-2 text-xl font-semibold text-gray-700"></h3>
    </div>
</x-filament-widgets::widget>
