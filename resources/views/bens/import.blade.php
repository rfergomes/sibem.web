@extends('layouts.app')

@section('title', 'Importar Bens - SIBEM')

@section('content')
    <div class="max-w-2xl mx-auto animate-fadeIn">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Importação de Bens</h1>
            <a href="{{ route('bens.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-900">Voltar para
                Lista</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-800">Upload de Arquivo Excel</h2>
                <p class="text-sm text-gray-500 mt-1">Carregue a planilha (.xlsx, .xls) com o cadastro legado.</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-600 border border-red-100 text-sm font-bold flex gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('bens.import.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div
                    class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 transition-colors hover:border-blue-400">
                    <input type="file" name="file" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="text-center pointer-events-none">
                        <p class="text-sm font-bold text-gray-600 uppercase tracking-widest">Clique ou arraste o arquivo</p>
                        <p class="text-xs text-gray-400 mt-2">Suporta XLSX, XLS e CSV</p>
                    </div>
                </div>



                <div class="bg-blue-50 p-4 rounded-lg text-xs text-blue-800 space-y-2">
                    <p class="font-bold uppercase tracking-widest mb-1">Estrutura Esperada:</p>
                    <div class="flex gap-4">
                        <span class="px-2 py-1 bg-white rounded border border-blue-100 font-mono">cod_bem</span>
                        <span class="px-2 py-1 bg-white rounded border-blue-100 font-mono">descricao</span>
                        <span class="px-2 py-1 bg-white rounded border-blue-100 font-mono">status</span>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98]">
                    PROCESSAR IMPORTAÇÃO
                </button>
            </form>
        </div>
    </div>

    <!-- SheetJS for client-side XLS conversion -->
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', async function (e) {
                    e.preventDefault(); // Prevent default submission to handle conversion

                    const btn = this.querySelector('button[type="submit"]');
                    const fileInput = this.querySelector('input[name="file"]');
                    const file = fileInput.files[0];

                    // UI Loading State
                    const originalBtnText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = `
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    ${file && file.name.toLowerCase().endsWith('.xls') ? 'CONVERTENDO E ENVIANDO...' : 'PROCESSANDO...'}
                                `;
                    btn.classList.add('opacity-75', 'cursor-wait');

                    // Check for legacy XLS file
                    if (file && file.name.toLowerCase().endsWith('.xls')) {
                        try {
                            console.log("Legacy XLS detected. Starting client-side conversion...");

                            // Read file as ArrayBuffer
                            const data = await file.arrayBuffer();

                            // Parse Workbook
                            const workbook = XLSX.read(data);

                            // Write as XLSX (Blob)
                            const xlsxData = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
                            const blob = new Blob([xlsxData], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

                            // Create new File object
                            const newFileName = file.name.replace(/\.xls$/i, '.xlsx');
                            const newFile = new File([blob], newFileName, { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

                            // Replace input file using DataTransfer
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(newFile);
                            fileInput.files = dataTransfer.files;

                            console.log(`Converted to ${newFileName}. Uploading...`);

                        } catch (error) {
                            console.error("Conversion failed:", error);

                            // Reset UI
                            btn.innerHTML = originalBtnText;
                            btn.disabled = false;
                            btn.classList.remove('opacity-75', 'cursor-wait');

                            alert("O arquivo XLS é muito grande ou complexo para ser convertido automaticamente pelo navegador.\n\nPor favor, salve-o como '.xlsx' usando o Excel ou Planilhas Google antes de enviar.");
                            return; // STOP SUBMISSION
                        }
                    }

                    // Submit the form
                    form.submit();
                });
            }
        });
    </script>
@endsection