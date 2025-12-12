@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #f6abeb;">
                    <h4 class="mb-0">{{ __('Reportes Semanales') }}</h4>
                </div>
                <div class="card-body p-4">
                    <p class="lead text-center">
                        Aquí se muestran tus estadísticas de hábitos de la semana del **{{ $fechaInicio }}** al **{{ $fechaFin }}**.
                    </p>

                    <hr class="my-4">

                    <h5 class="mb-4 text-center text-primary">Resumen Semanal de Hábitos</h5>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Agua:</strong>
                            @if ($promedioAguaDiarioVasos > 0)
                                <span class="badge bg-primary rounded-pill">Promedio: {{ $promedioAguaDiarioVasos }} vasos/día</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Sin datos registrados</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Sueño:</strong>
                            @if ($promedioHorasSuenoDiario > 0)
                                <span class="badge bg-info rounded-pill text-dark">Promedio: {{ $promedioHorasSuenoDiario }} horas/noche</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Sin datos registrados</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Actividad Física:</strong>
                            @if ($totalMinutosActividadSemana > 0)
                                <span class="badge bg-success rounded-pill">Total: {{ $totalMinutosActividadSemana }} min | Intensidad más común: {{ ucfirst($intensidadMasComun) }}</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Sin datos registrados</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Alimentación:</strong>
                            <span class="badge bg-warning rounded-pill text-dark">{{ $patronAlimentacion }}</span>
                        </li>
                    </ul>

                    <p class="text-center text-muted fst-italic mt-4">
                        (Estos datos se calculan automáticamente de tus registros.)
                    </p>

                    <hr class="my-5">

                    <h5 class="mb-4 text-center text-primary">Gráficos de tu Progreso</h5>

                    {{-- Gráfico de Agua --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-center text-secondary mb-3">Consumo de Agua (Litros)</h6>
                            <canvas id="aguaChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>

                    {{-- Gráfico de Sueño --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-center text-secondary mb-3">Horas de Sueño</h6>
                            <canvas id="suenoChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>

                    {{-- Gráfico de Actividad Física --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-center text-secondary mb-3">Intensidad de Actividad Física</h6>
                            <canvas id="actividadChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>

                    {{-- Gráfico de Alimentación --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-center text-secondary mb-3">Tipos de Comida Registrados</h6>
                            <canvas id="alimentacionChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver al Panel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de Agua
    const aguaLabels = {!! $aguaChartLabels !!};
    const aguaData = {!! $aguaChartData !!};
    const aguaCtx = document.getElementById('aguaChart').getContext('2d');
    new Chart(aguaCtx, {
        type: 'bar', // Tipo de gráfico: barras
        data: {
            labels: aguaLabels,
            datasets: [{
                label: 'Litros de Agua',
                data: aguaData,
                backgroundColor: 'rgba(54, 162, 235, 0.7)', // Azul
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Litros'
                    }
                }
            }
        }
    });

    // Datos de Sueño
    const suenoLabels = {!! $suenoChartLabels !!};
    const suenoData = {!! $suenoChartData !!};
    const suenoCtx = document.getElementById('suenoChart').getContext('2d');
    new Chart(suenoCtx, {
        type: 'line', // Tipo de gráfico: línea
        data: {
            labels: suenoLabels,
            datasets: [{
                label: 'Horas de Sueño',
                data: suenoData,
                backgroundColor: 'rgba(153, 102, 255, 0.7)', // Morado
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Horas'
                    }
                }
            }
        }
    });

    // Datos de Actividad Física
    const actividadLabels = {!! $actividadChartLabels !!};
    const actividadData = {!! $actividadChartData !!};
    const actividadCtx = document.getElementById('actividadChart').getContext('2d');
    new Chart(actividadCtx, {
        type: 'pie', // Tipo de gráfico: pastel
        data: {
            labels: actividadLabels,
            datasets: [{
                label: 'Actividades',
                data: actividadData,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)', // Verde azulado (Baja)
                    'rgba(255, 206, 86, 0.7)', // Amarillo (Media)
                    'rgba(255, 99, 132, 0.7)', // Rojo (Alta)
                    'rgba(200, 200, 200, 0.7)' // Gris (Sin Registrar)
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(200, 200, 200, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: false
                }
            }
        }
    });

    // Datos de Alimentación
    const alimentacionLabels = {!! $alimentacionChartLabels !!};
    const alimentacionData = {!! $alimentacionChartData !!};
    const alimentacionCtx = document.getElementById('alimentacionChart').getContext('2d');
    new Chart(alimentacionCtx, {
        type: 'bar', // Tipo de gráfico: barras
        data: {
            labels: alimentacionLabels,
            datasets: [{
                label: 'Número de Registros',
                data: alimentacionData,
                backgroundColor: 'rgba(255, 159, 64, 0.7)', // Naranja
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad'
                    },
                    ticks: {
                        stepSize: 1 // Solo enteros para contar registros
                    }
                }
            }
        }
    });

});
</script>
@endpush