@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-10 shadow">

        <div class="text-center mb-8">
            <h2 class="font-bold text-lg">PEMERINTAH PROVINSI KALIMANTAN SELATAN</h2>
            <h3 class="font-bold">BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH</h3>
            <p>Banjarbaru</p>
        </div>

        <div class="text-center font-bold mb-6">
            NOTA DINAS
        </div>

        <table class="w-full mb-6">
            <tr>
                <td width="150">Kepada</td>
                <td>: {{ $nota->kepada->nama }}</td>
            </tr>
            <tr>
                <td>Melalui</td>
                <td>: {{ $nota->melalui->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Dari</td>
                <td>: {{ $nota->dari->nama }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($nota->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>: {{ $nota->perihal }}</td>
            </tr>
        </table>

        <div class="text-justify leading-relaxed mb-6 indent-8">
            Sehubungan dengan undangan <b> {{ $nota->asal_undangan }}</b>, dengan hormat diusulkan
            <b>
                @foreach ($groupedPegawai as $jabatan => $jumlah)
                    {{ $jumlah }}
                    ({{ \Illuminate\Support\Str::ucfirst(terbilang($jumlah)) }})
                    orang {{ \Illuminate\Support\Str::title($jabatan) }}@if (!$loop->last)
                        ,
                    @endif
                @endforeach
            </b>
           untuk melaksanakan perjalanan dinas biasa yakni perjalanan dinas dalam rangka {{ $nota->perihal}}
        </div>

        <div class="text-justify leading-relaxed mb-6 indent-8">
            Pembebanan biaya perjalanan dinas menggunakan Sub Kegiatan pada
            DPA Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Kalimantan Selatan
            Tahun Anggaran {{ date('Y') }} yaitu
            <b>
                {{ $nota->subKegiatan->nomor_rekening }}
                {{ $nota->subKegiatan->nama_kegiatan }}
            </b>.
        </div>

        <div class="text-justify leading-relaxed mb-8 indent-8">
            Demikian disampaikan, apabila berkenan mohon persetujuan Bapak untuk
            penandatanganan SPT sebagaimana terlampir. Atas persetujuan dan
            perkenannya diucapkan terima kasih.
        </div>

        <div class="mt-12 text-right">
            <p class="mt-16 font-bold">{{ optional($nota->dari)->jabatan }}</p>
            <p class="mt-16 font-bold">{{ optional($nota->dari)->nama }}</p>
        </div>

        @if (auth()->user()->role->name == 'kepala_bidang')
            <div class="mt-10">
                <form action="{{ route('nota-dinas.approve-kabid', $nota->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <x-ui.button variant="success">Approve Final</x-ui.button>
                </form>
            </div>
        @endif

    </div>
@endsection
