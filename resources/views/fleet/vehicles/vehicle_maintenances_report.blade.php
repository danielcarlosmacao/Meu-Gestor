@extends('layouts.header')

@section('title', 'Relatório de Manutenções por Período')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/fleet-module.css') }}">

    <div class="fleet-page">

        <div class="fleet-container">

            {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

            <div class="fleet-page-header">

                <div class="fleet-page-heading">

                    <span class="fleet-page-icon">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </span>

                    <div>

                        <h2 class="fleet-page-title">
                            Relatório de Manutenções
                        </h2>

                        <p class="fleet-page-subtitle">
                            Gere o relatório das manutenções realizadas em um período específico.
                        </p>

                    </div>

                </div>

            </div>

            {{-- ============================================================
            CARD DO RELATÓRIO
        ============================================================= --}}

            <div class="fleet-card">

                <div class="fleet-card-header">

                    <div>

                        <h5 class="fleet-card-title">
                            Período do relatório
                        </h5>

                        <p class="fleet-card-subtitle">
                            Informe a data inicial e final para visualizar ou baixar o relatório.
                        </p>

                    </div>

                    <span class="fleet-summary-icon">
                        <i class="bi bi-calendar-range"></i>
                    </span>

                </div>

                <div class="fleet-card-body">

                    <form method="GET" action="{{ route('vehicle-maintenance.report.pdf') }}" target="_blank"
                        class="needs-validation" data-fleet-form novalidate>

                        <div class="row g-4 align-items-end">

                            {{-- Data inicial --}}

                            <div class="col-md-4">

                                <label for="start_date" class="form-label">

                                    Data inicial

                                    <span class="fleet-required">*</span>
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-event"></i>
                                    </span>

                                    <input type="date" name="start_date" id="start_date"
                                        value="{{ old('start_date', request('start_date', date('Y-m-01'))) }}"
                                        class="form-control" required>

                                    <div class="invalid-feedback">
                                        Informe a data inicial.
                                    </div>

                                </div>

                            </div>

                            {{-- Data final --}}

                            <div class="col-md-4">

                                <label for="end_date" class="form-label">

                                    Data final

                                    <span class="fleet-required">*</span>
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-check"></i>
                                    </span>

                                    <input type="date" name="end_date" id="end_date"
                                        value="{{ old('end_date', request('end_date', date('Y-m-d'))) }}"
                                        class="form-control" required>

                                    <div class="invalid-feedback">
                                        Informe a data final.
                                    </div>

                                </div>

                            </div>

                            {{-- Ações --}}

                            <div class="col-md-4">

                                <div class="d-grid d-sm-flex gap-2">

                                    <button type="submit" name="action" value="view"
                                        class="btn btn-outline-primary flex-fill" data-report-action="view">

                                        <i class="bi bi-eye"></i>
                                        Visualizar
                                    </button>

                                    <button type="submit" name="action" value="download"
                                        class="btn btn-outline-success flex-fill" data-report-action="download">

                                        <i class="bi bi-download"></i>
                                        Baixar PDF
                                    </button>

                                </div>

                            </div>

                        </div>

                        {{-- Informações do período --}}

                        <div class="fleet-alert fleet-alert-info mt-4 mb-0">

                            <i class="bi bi-info-circle-fill"></i>

                            <div>

                                <strong>
                                    Período selecionado
                                </strong>

                                <div class="mt-1">

                                    <span id="reportPeriodText">
                                        Do primeiro dia do mês atual até hoje.
                                    </span>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


        </div>

    </div>

    <script src="{{ asset('js/fleet-module.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const periodText = document.getElementById('reportPeriodText');

            if (!startDateInput || !endDateInput || !periodText) {
                return;
            }

            function formatDate(dateValue) {
                if (!dateValue) {
                    return null;
                }

                const parts = dateValue.split('-');

                if (parts.length !== 3) {
                    return dateValue;
                }

                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }

            function updatePeriodText() {
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;

                if (startDate && endDate) {
                    periodText.textContent =
                        `De ${formatDate(startDate)} até ${formatDate(endDate)}.`;
                    return;
                }

                if (startDate) {
                    periodText.textContent =
                        `A partir de ${formatDate(startDate)}.`;
                    return;
                }

                if (endDate) {
                    periodText.textContent =
                        `Até ${formatDate(endDate)}.`;
                    return;
                }

                periodText.textContent = 'Selecione o período do relatório.';
            }

            function validatePeriod() {
                if (
                    startDateInput.value &&
                    endDateInput.value &&
                    startDateInput.value > endDateInput.value
                ) {
                    endDateInput.setCustomValidity(
                        'A data final deve ser igual ou posterior à data inicial.'
                    );
                } else {
                    endDateInput.setCustomValidity('');
                }
            }

            startDateInput.addEventListener('change', function() {
                endDateInput.min = startDateInput.value;
                validatePeriod();
                updatePeriodText();
            });

            endDateInput.addEventListener('change', function() {
                validatePeriod();
                updatePeriodText();
            });

            if (startDateInput.value) {
                endDateInput.min = startDateInput.value;
            }

            validatePeriod();
            updatePeriodText();
        });
    </script>

@endsection
