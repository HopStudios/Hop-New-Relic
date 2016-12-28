
$(function() {
	var ctx = document.getElementById("nr-chart");
	var lineChart = null;

	$('.chart-form').on('submit', function(e) {
		$(this).find('.btn.submit').addClass('work');
		$('#nr-data-error').hide();
		var form_data = $(this).serializeArray();
		var action_url = $(this).attr("action");
		$.ajax({
			type: "GET",
			url: action_url,
			dataType: "json",
			data: form_data,
			success: function(data, textStatus, jqXHR){
				console.log(data);
				if (lineChart !== null) {
					lineChart.destroy();
				}
				if (data.error === true) {
					$('#nr-data-error').html(data.message);
					$('#nr-data-error').show();
				} else {
					lineChart = new Chart(ctx, {
						type: 'line',
						data: data,
						options: {
							responsive: true,
							maintainAspectRatio: false,
							hover: {
								mode: 'x-axis'
							},
							tooltips: {
								mode: 'x-axis'
							},
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero:false
									}
								}]
							},
							elements: {
								point: {
									hitRadius: 4
								}
							}
						}
					});
				}
				
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#nr-data-error').html("Error when retrieving data.");
				$('#nr-data-error').show();
			},
			complete: function(jqXHR, textStatus) {
				$('.chart-form').find('.btn.submit').removeClass('work');
			}
		});
		
		e.preventDefault();
	});

	$('select[name="metric_type"]').on('change', function(e) {
		$('.data-select').hide();
		if ($(this).val() != "") {
			$('.'+$(this).val()+'-data-select').show();
		}
	});

	$('select[name="custom_dataset_app_metric_name"]').on('change', function(e) {
		// console.log($(this).val());
		if (metric_values_app !== undefined) {
			selected_app_metric_values = metric_values_app[$(this).val()];
			if (selected_app_metric_values !== undefined) {
				// Empty current list of values
				$('select[name="custom_dataset_app_metric_value"]').html('');
				// Add values related to selected metric name
				$.each(selected_app_metric_values, function() {
					$('select[name="custom_dataset_app_metric_value"]').append($("<option />").val(this).text(this));
				});
			}
			// console.log(selected_app_metric_values);
		}
	});

	$('select[name="custom_dataset_server_metric_name"]').on('change', function(e) {
		// console.log($(this).val());
		if (metric_values_server !== undefined) {
			selected_server_metric_values = metric_values_server[$(this).val()];
			if (selected_server_metric_values !== undefined) {
				// Empty current list of values
				$('select[name="custom_dataset_server_metric_value"]').html('');
				// Add values related to selected metric name
				$.each(selected_server_metric_values, function() {
					$('select[name="custom_dataset_server_metric_value"]').append($("<option />").val(this).text(this));
				});
			}
			// console.log(selected_server_metric_values);
		}
	});
});