<br />
<div class="row">
	<div class="col-md-12 mb-3">
		<label class="h5 mb-3"><?php echo $this->lang->line('ms_purchase_items'); ?></label>
	</div>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-striped table" id="ms_table_items">
				<thead class="thead-light">
					<tr>
						<th><?php echo $this->lang->line('xin_title_item'); ?></th>
						<th><?php echo $this->lang->line('xin_project'); ?></th>
						<th><?php echo $this->lang->line('ms_ref_title_unit_price'); ?></th>
						<th><?php echo $this->lang->line('xin_title_qty'); ?></th>
						<th style="min-width: 150px">
							<?php echo $this->lang->line('xin_title_sub_total'); ?>
						</th>
						<!-- <th class="text-center"><?php echo $this->lang->line('xin_action'); ?></th> -->
					</tr>
				</thead>
				<tbody id="formRow"></tbody>
				<tfoot>
					<tr>
						<th colspan="5"><?= $this->lang->line('ms_delivery_fee'); ?></th>
						<td>
							<strong><?= $this->Xin_model->currency_sign($record->ref_delivery_fee);
									?></strong>
						</td>
					</tr>
					<tr>
						<th colspan="5"><?= $this->lang->line('xin_amount'); ?></th>
						<td>
							<strong class="text-danger"><?= $this->Xin_model->currency_sign($record->amount +
															$record->ref_delivery_fee); ?></strong>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<br />
<div class="row">
	<div class="col-md-12 mb-3">
		<label class="h5 mb-3"><?php echo $this->lang->line('ms_purchase_items'); ?></label>
	</div>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-striped table-borderless table" id="ms_table_items">
				<!-- <thead class="thead-light"> -->
				<thead>
					<tr>
						<th style="min-width: 100px">
							<?php echo $this->lang->line('xin_title_item'); ?>
						</th>
						<th style="min-width: 100px">
							<?php echo $this->lang->line('xin_project'); ?>
						</th>
						<th><?php echo $this->lang->line('xin_title_taxes'); ?></th>
						<th><?php echo $this->lang->line('xin_discount'); ?></th>
						<th style="min-width: 100px; max-width: 200px">
							<?php echo $this->lang->line('xin_title_qty'); ?>
						</th>
						<th style="min-width: 100px">
							<?php echo $this->lang->line('ms_title_unit_price'); ?>
						</th>
						<th style="min-width: 150px" class="text-center">
							<?php echo $this->lang->line('xin_title_sub_total'); ?>
						</th>
					</tr>
				</thead>
				<tbody id="formRow"></tbody>
				<tfoot>
					<tr>
						<td colspan="6" class="text-right">
							<strong><?php echo $this->lang->line('xin_title_sub_total'); ?></strong>
						</td>
						<td>
							<strong id="discount_amount"><?= $this->Xin_model->currency_sign($record->subtotal);
															?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="6" class="text-right">
							<strong><?php echo $this->lang->line('xin_discount'); ?></strong>
						</td>
						<td>
							<strong id="discount_amount"><?= $this->Xin_model->currency_sign($record->discount);
															?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="6" class="text-right">
							<strong><?php echo $this->lang->line('xin_title_taxes'); ?></strong>
						</td>
						<td>
							<strong id="tax_amount"><?= $this->Xin_model->currency_sign($record->tax); ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="6" class="text-right">
							<strong><?php echo $this->lang->line('ms_delivery_fee'); ?></strong>
						</td>
						<td>
							<strong id="delivery_fee"><?= $this->Xin_model->currency_sign($record->delivery_fee);
														?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="6" class="text-right">
							<strong><?php echo $this->lang->line('xin_amount'); ?></strong>
						</td>
						<td>
							<strong class="text-danger" id="grand_total"><?= $this->Xin_model->currency_sign($record->amount);
																			?></strong>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>


public function get_ajax_pr()
{
$po_number = $this->input->get('pr_number');
$pr_data = $this->Purchase_model->read_pr_by_pr_number($pr_number);
$pr_items = $this->Purchase_items_model->read_items_pr_by_pr_number($pr_number)->result();

// dd($pr_data);
if (!is_null($pr_data) && !is_null($pr_items)) {
$output = [
'data' => $pr_data,
'items' => $pr_items
];
} else {
$output = false;
}
echo json_encode($output);
exit();
}
public function insert()
{
// Get the input data
$pr_number = $this->input->post('pr_number');
$po_number = $this->input->post('po_number');

// if ($this->input->is_ajax_request()) {
if (true) {

/* Define return | here result is used to return user data and error for error message */
$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
$Return['csrf_hash'] = $this->security->get_csrf_hash();

/* Server side PHP input validation */
if ($this->input->post('vendor_id') === '') {
$Return['error'] = $this->lang->line('ms_error_vendor_field');
} else if ($this->input->post('warehouse_assign') === '') {
$Return['error'] = $this->lang->line('ms_error_warehouse_assign_field');
} else if ($this->input->post('faktur_number') === '') {
$Return['error'] = $this->lang->line('ms_error_faktur_number_field');
} else if ($this->input->post('date') === '') {
$Return['error'] = $this->lang->line('ms_error_date_field');
} else if ($this->input->post('termin') === '') {
$Return['error'] = $this->lang->line('ms_error_field');
}


if (is_null($this->input->post('row_item_name'))) {
$Return['error'] = $this->lang->line('ms_error_item_empty_data');
}

if ($Return['error'] != '') {
$this->output($Return);
exit();
}

$data_po = array(
'pr_id' => 0,
'vendor_id' => $this->input->post('vendor'),
'po_number' => $po_number,
'warehouse_assign' => $this->input->post('warehouse_assign'),
'faktur_number' => $this->input->post('faktur_number'),
'date' => $this->input->post('date') ?? date("Y-m-d"),
'due_date' => $this->input->post('due_date') ?? date("Y-m-d"),
'termin' => $this->input->post('select_due_date'),
'expedition_name' => $this->input->post('expedition_name'),
'status' => 0,
'reference' => $this->input->post('reference'),
'attachment' => $this->input->post('attachment') ?? '',
'amount' => $this->input->post('amount'),
);

// $insert_po = $this->Purchase_model->insert_po($data_po);

$item_insert = [];

for ($i = 0; $i < count($this->input->post('row_item_id')); $i++) {
	$item_insert[] = [
	'po_number' => $po_number,
	'product_id' => $this->input->post('row_item_id')[$i],
	'product_name' => $this->input->post('row_item_name')[$i],
	'project_id' => $this->input->post('row_project_id')[$i],
	'tax_id' => $this->input->post('row_tax_id')[$i] ?? 0, //override to 0
	'tax_rate' => $this->input->post('row_tax_rate')[$i],
	'discount_id' => $this->input->post('row_discount_id')[$i] ?? 0, //override to 0
	'discount_rate' => $this->input->post('row_discount_rate')[$i],
	'quantity' => $this->input->post('row_qty')[$i],
	'price' => $this->input->post('row_item_price')[$i],
	'amount' => $this->input->post('row_amount')[$i],

	];
	}

	$this->output([
	'data' => $data_po,
	'items' => $item_insert
	]);
	// if ($insert_po) {

	// // $this->insert_items_po($po_number, true);
	// } else {
	// $Return['error'] = $this->lang->line('xin_error_msg');
	// $this->output($Return);
	// }
	}
	}

	public function insert_items_po($po_number, $batch = false)
	{
	$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
	$Return['csrf_hash'] = $this->security->get_csrf_hash();

	if ($batch) {
	$item_insert = [];

	for ($i = 0; $i < count($this->input->post('row_item_id')); $i++) {
		$item_insert[] = [
		'po_number' => $po_number,
		'product_id' => $this->input->post('row_item_id')[$i],
		'product_name' => $this->input->post('row_item_name')[$i],
		'project_id' => $this->input->post('row_project_id')[$i],
		'tax_id' => $this->input->post('row_tax_id')[$i],
		'tax_rate' => $this->input->post('row_tax_rate')[$i],
		'discount_id' => $this->input->post('row_discount_id')[$i],
		'discount_rate' => $this->input->post('row_discount_rate')[$i],
		'quantity' => $this->input->post('row_qty')[$i],
		'price' => $this->input->post('row_item_price')[$i],
		'amount' => $this->input->post('row_amount')[$i],

		];
		}
		$result = $this->Purchase_items_model->insert_items_po($item_insert, true);
		} else {
		$item_insert = [
		'item_name' => $this->input->post('row_item_name'),
		'pr_number' => $pr_number,
		'project_id' => $this->input->post('row_project_id'),
		'quantity' => $this->input->post('row_qty'),
		'ref_price' => $this->input->post('row_ref_amount'),
		'amount' => $this->input->post('row_amount'),
		];
		$result = $this->Purchase_items_model->insert_items_po($item_insert);
		}

		if ($result) {
		$Return['result'] = $this->lang->line('ms_trans_added');
		} else {
		$Return['error'] = $this->lang->line('xin_error_msg');
		}

		$this->output($Return);
		exit;
		}

		public function get_ajax_table()
		{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
		redirect('admin/');
		}
		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));


		$records = $this->Purchase_model->get_all_po();

		$data = array();

		foreach ($records->result() as $r) {

		$po_number = '<a href="' . site_url() . 'admin/purchase_orders/view/' . $r->po_number . '/">' . $r->po_number . '</a>';

		if (in_array('492', $role_resources_ids)) { //edit
		$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_id="' . $r->po_id . '" data-field_type="purchase_requisitions"><span class="fas fa-pencil-alt"></span></button></span>';
		} else {
		$edit = '';
		}
		if (in_array('493', $role_resources_ids)) { // delete
		$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->po_id . '" data-token_type="purchase_requisitions"><span class="fas fa-trash-restore"></span></button></span>';
		} else {
		$delete = '';
		}

		/// get vendor
		$vendor = $this->Vendor_model->read_vendor_information($r->vendor_id);
		// var_dump($vendor);
		if (!is_null($vendor)) {
		$vendor = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
		} else {
		$vendor = '--';
		}

		$combhr = $edit . $delete;

		$data[] = array(
		$combhr,
		$po_number,
		$vendor,
		$this->Xin_model->set_date_format($r->date),
		po_stats($r->status),
		strlen($r->reference) >= 20 ? substr($r->reference, 0, 20) . '...' : $r->reference ?? '--',
		$this->Xin_model->currency_sign($r->amount),
		);
		}


		$output = array(
		"draw" => $draw,
		"recordsTotal" => $records->num_rows(),
		"recordsFiltered" => $records->num_rows(),
		"data" => $data
		);
		echo json_encode($output);
		exit();
		}

		public function get_ajax_table_items()
		{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata(' ');
		if (empty($session)) {
		redirect('admin/');
		}
		$id = $this->uri->segment(4);

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));


		$records = $this->Purchase_items_model->read_items_po_by_po_number($id);
		$data = array();
		foreach ($records->result() as $i => $r) {

		$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
		if (!is_null($project)) {
		$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
		} else {
		$project_name = '--';
		}

		// get product
		$product = $this->Xin_model->query("SELECT * FROM ms_products LEFT JOIN ms_measurement_units ON ms_products.uom_id=ms_measurement_units.uom_id WHERE product_id=" . $r->product_id)->row();
		if (!is_null($product)) {
		$uom_name = $product->uom_name;
		} else {
		$uom_name = '--';
		}
		$data[] = [
		$r->product_name,
		$project_name,
		$r->quantity,
		$uom_name,
		];
		}
		$output = array(
		"draw" => $draw,
		"recordsTotal" => $records->num_rows(),
		"recordsFiltered" => $records->num_rows(),
		"data" => $data
		);
		echo json_encode($output);
		exit();
		}