@extends('layouts.app')

@section('title', 'Detail Konversi JP')
@section('page_title', 'Detail Kegiatan Konversi JP')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('konversi.index') }}">Konversi JP</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $konversi->nama_kegiatan }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('konversi.edit', $konversi) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Data
        </a>
        <a href="{{ route('konversi.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-light text-primary border px-3 py-2 fs-6">{{ $konversi->kategori_konversi }}</span>
                <span class="badge bg-{{ $konversi->status_validasi == 'Disetujui' ? 'success' : 'warning' }} px-3 py-2">
                    {{ $konversi->status_validasi }}
                </span>
            </div>

            <h4 class="fw-bold text-dark mb-1">{{ $konversi->nama_kegiatan }}</h4>
            <p class="text-muted small mb-4">Dilaksanakan pada {{ $konversi->tanggal_kegiatan->format('d F Y') }}</p>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border text-center">
                        <span class="text-muted small d-block mb-1">Jumlah JP Diakui</span>
                        <span class="fs-3 fw-bold text-primary">{{ $konversi->jumlah_jp }} JP</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border text-center">
                        <span class="text-muted small d-block mb-1">Jumlah JP Dibayarkan</span>
                        <span class="fs-3 fw-bold text-secondary">{{ $konversi->jp_dibayarkan }} JP</span>
                    </div>
                </div>
            </div>

            <table class="table table-bordered small mb-4">
                <tr>
                    <th style="width: 30%;" class="table-light">Nama Widyaiswara</th>
                    <td class="fw-bold">{{ $konversi->widyaiswara->nama_gelar }} ({{ $konversi->widyaiswara->jabatan }})</td>
                </tr>
                <tr>
                    <th class="table-light">Nomor SK / Surat Tugas</th>
                    <td>{{ $konversi->nomor_sk ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="table-light">Informasi Pembayaran</th>
                    <td>{{ $konversi->informasi_pembayaran ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="table-light">Keterangan / Output</th>
                    <td>{{ $konversi->keterangan ?? '-' }}</td>
                </tr>
            </table>

            <div class="text-end">
                <a href="{{ route('konversi.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Konversi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
