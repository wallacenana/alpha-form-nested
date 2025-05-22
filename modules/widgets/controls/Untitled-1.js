<script>
    (function(){Chart.elements.Rectangle.prototype.draw = function () {
        var ctx = this._chart.ctx;
        var vm = this._view;
        var left, right, top, bottom;
        var radius = { $radius };
        var width = { $width };

        if (!vm.horizontal) {
            const barWidth = { $width }; // <- aplica aqui
            left = vm.x - barWidth / 2;
            right = vm.x + barWidth / 2;
            top = vm.y;
            bottom = vm.base;
        } else {
            const barWidth = { $width };
            left = vm.base;
            right = vm.x;
            top = vm.y - barWidth / 2;
            bottom = vm.y + barWidth / 2;
        }


        ctx.beginPath();
        ctx.fillStyle = vm.backgroundColor;
        ctx.strokeStyle = vm.borderColor;
        ctx.lineWidth = vm.borderWidth;

        var cornerRadius = Math.min(radius, Math.abs(bottom - top) / 2, Math.abs(right - left) / 2);
        var x = left;
        var y = top;
        var width = right - left;
        var height = bottom - top;

        ctx.moveTo(x + cornerRadius, y);
        ctx.lineTo(x + width - cornerRadius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + cornerRadius);
        ctx.lineTo(x + width, y + height - cornerRadius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - cornerRadius, y + height);
        ctx.lineTo(x + cornerRadius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - cornerRadius);
        ctx.lineTo(x, y + cornerRadius);
        ctx.quadraticCurveTo(x, y, x + cornerRadius, y);

        ctx.fill();
        if (vm.borderWidth) ctx.stroke();
    };

    const ctx = document.getElementById('chart_{$widget_id}').getContext('2d');
    new Chart(ctx, {
        type: '{$chart_type}',
    data: {
        labels: " . json_encode($labels) . ",
    datasets: [{
        label: '{$element_name}',
    backgroundColor: " . json_encode($bg_colors) . ",
    borderColor: " . json_encode($bd_colors) . ",
    data: " . json_encode($values) . ",
    borderWidth: " . intval($settings['dataset_border_width']['size']) . ",
                        }]
                    },
    options: {
        responsive: true,
    legend: {
        display: {$show_legend}
                        },
    scales: {
        xAxes: [{
        display: " . ($show_axis_x ? 'true' : 'false') . ",
    gridLines: {
        display: " . ($y_grid ? 'true' : 'false') . ",
    drawBorder: " . ($x_border ? 'true' : 'false') . ",
    borderDash: " . json_encode(
    $settings['x_grid_style'] === 'dashed' ? [10, 5] : ($settings['x_grid_style'] === 'dotted' ? [2, 3] : [])
    ) . ",
    color: '{$colorx}'
                                },
    ticks: {
        display: " . ($x_ticks ? 'true' : 'false') . ",
    maxTicksLimit: {$x_max_ticks}
                                }
                            }],
    yAxes: [{
        display: " . ($show_axis_y ? 'true' : 'false') . ",
    gridLines: {
        display: " . ($x_grid ? 'true' : 'false') . ",
    drawBorder: " . ($y_border ? 'true' : 'false') . ",
    borderDash: " . json_encode($settings['y_grid_style'] === 'dashed' ? [10, 5] : ($settings['y_grid_style'] === 'dotted' ? [2, 3] : [])) . ",
    color: '{$colory}'
                                },
    ticks: {
        display: " . ($y_ticks ? 'true' : 'false') . ",
    maxTicksLimit: {$y_max_ticks},
    min: 0,
    stepSize: 10,
    beginAtZero: true,
                                }
                            }]
                        }
                    }
                });
            })();
</script>