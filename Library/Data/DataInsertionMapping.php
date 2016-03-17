<?php

$dataMapping = [
    'product' => [
        0 => [
            'base_instance_id' => '{{ product_instances:0 }}',
            'parent_id' => null,
            'last_modified' => '2015-09-22 09:00:00',
            'is_for_internal_use' => false
        ]
    ],
    'product_instances' => [
        0 => [
            'product_id' => '{{ product:0 }}',
            'name' => 'Quotation & Specification',
            'description' => 'Quotation & Specification Demo Product',
            'default_cost' => 30.00,
            'default_price' => 0.00,
            'time_required' => 120,
            'is_enabled' => true,
            'created_on' => '2015-09-22 09:00:00',
            'status_id' => null
        ]
    ],
    'product_status' => [
        0 => [
            'product_instance_id' => '{{ product_instances:0 }}',
            'name' => 'Enquiry Received',
            'sort_order' => 0,
            'is_final' => false
        ],
        1 => [
            'product_instance_id' => '{{ product_instances:0 }}',
            'name' => 'Allocate Expert',
            'sort_order' => 1,
            'is_final' => false
        ],
        2 => [
            'product_instance_id' => '{{ product_instances:0 }}',
            'name' => 'Expert Allocated',
            'sort_order' => 2,
            'is_final' => false
        ],
        3 => [
            'product_instance_id' => '{{ product_instances:0 }}',
            'name' => 'Report Generation',
            'sort_order' => 3,
            'is_final' => false
        ],
        4 => [
            'product_instance_id' => '{{ product_instances:0 }}',
            'name' => 'Quality Control',
            'sort_order' => 4,
            'is_final' => false
        ],
        5 => [
            'product_instance_id' => '{{ product_instances:0 }}',
            'name' => 'Complete',
            'sort_order' => 5,
            'is_final' => true
        ]
    ],
    'product_instance_view' => [
        0 => [
            'product_instance_id' => '{{ product_instance:0 }}',
            'product_status_id' => '{{ product_status:0 }}',
            'content' => '<div class="alert alert-info">
<div class="alert-icon"><i class="fa fa-info"></i></div>
    <div class="notification-info">
        <strong>Details</strong>
        <br>
        <span class="notification-message">
            Please enter all of the following details to move this job to the next status.
        </span>
    </div>
</div>',
            'default' => false,
            'name' => 'Enquiry Received',
            'sort_order' => 0,
            'roles' => null
        ],
        1 => [
            'product_instance_id' => '{{ product_instance:0 }}',
            'product_status_id' => '{{ product_status:1 }}',
            'content' => '<div class="alert alert-info">
<div class="alert-icon"><i class="fa fa-info"></i></div>
    <div class="notification-info">
        <strong>Expert</strong>
        <br>
        <span class="notification-message">
            Please allocate an expert to carry out this quote.
        </span>
    </div>
</div>',
            'default' => false,
            'name' => 'Allocate Expert',
            'sort_order' => 1,
            'roles' => null
        ],
        2 => [
            'product_instance_id' => '{{ product_instance:0 }}',
            'product_status_id' => '{{ product_status:2 }}',
            'content' => '<div class="alert alert-info">
<div class="alert-icon"><i class="fa fa-info"></i></div>
    <div class="notification-info">
        <strong>Expert Report</strong>
        <br>
        <span class="notification-message">
            Please enter your report and confirm when you are happy with it to move it to the next status.
        </span>
    </div>
</div>',
            'default' => false,
            'name' => 'Expert Allocated',
            'sort_order' => 2,
            'roles' => null
        ],
        3 => [
            'product_instance_id' => '{{ product_instance:0 }}',
            'product_status_id' => '{{ product_status:3 }}',
            'content' => '<div class="alert alert-info">
<div class="alert-icon"><i class="fa fa-info"></i></div>
    <div class="notification-info">
        <strong>Report Generation</strong>
        <br>
        <span class="notification-message">
            Please upload any required images and generate your report to move this job to the next status.
        </span>
    </div>
</div>',
            'default' => false,
            'name' => 'Report Generation',
            'sort_order' => 3,
            'roles' => null
        ],
        4 => [
            'product_instance_id' => '{{ product_instance:0 }}',
            'product_status_id' => '{{ product_status:4 }}',
            'content' => '<div class="alert alert-info">
<div class="alert-icon"><i class="fa fa-info"></i></div>
    <div class="notification-info">
        <strong>Quality Control</strong>
        <br>
        <span class="notification-message">
            When this report has been emailed and/or printed and the job is complete, use the button to confirm the job is complete.
        </span>
    </div>
</div>',
            'default' => false,
            'name' => 'Quality Control',
            'sort_order' => 4,
            'roles' => null
        ],
        5 => [
            'product_instance_id' => '{{ product_instance:0 }}',
            'product_status_id' => '{{ product_status:5 }}',
            'content' => '<div class="alert alert-success">
<div class="alert-icon"><i class="fa fa-check"></i></div>
    <div class="notification-info">
        <strong>Completed</strong>
        <br>
        <span class="notification-message">
            This job is now complete.
        </span>
    </div>
</div>',
            'default' => false,
            'name' => 'Complete',
            'sort_order' => 5,
            'roles' => null
        ],
    ],
    'instruction_question' => [
        0 => [
            'question_type_id' => 2,
            'name' => 'Contact Name for Inspection',
            'key' => 'contact_for_inspection', // This has a unique index on the column so check if exists first and obtain the ID.
            'deleted' => false
        ]
    ],
    'instruction_group' => [
        0 => [
            'product_status_id' => '{{ product_status:0 }}',
            'title' => 'Enquiry Received',
            'sort_order' => 0
        ]
    ],
    'instruction_group_question' => [
        0 => [
            'group_id' => '{{ instruction_group:0 }}',
            'question_id' => '{{ instruction_question:0 }}',
            'sort_order' => 0
        ]
    ],
    'product_view_wdiget' => [
        0 => [
            'product_instance_view_id' => '{{ product_instance_view:0 }}',
            'content' => '<p>Please fill out all the information then click <strong>Save</strong>.</p>', // Content to display at the top of the widget.
            'type' => 3, // Full form of instruction questions for the status that this product view is for.
            'sort_order' => 0,
            'config' => null
        ],
        1 => [
            'product_instance_view_id' => '{{ product_instance_view:2 }}',
            'content' => '<p>When you have completed your post-inspection report, save your inputs above and click the button below to mark it ready for report generation.</p>',
            'type' => 8, // Generic type; button that can move status on click. Requires a corresponding generic criteria event = criteria type 2.
            'sort_order' => 1,
            'config' => null
        ]
    ],
    
    // Completed fields for first status to move to next.
    'event_event' => [
        0 => [
            'event_type_id' => 1, // 1 = Status Change
            'product_status_id' => '{{ product_status:0 }}', // Status to be in for this event to fire.
            'product_instance_id' => '{{ product_instance:0 }}',
            'options' => serialize(
                [
                    'status_id' => '{{ product_status:1 }}' // Status to move the order line into.
                ]
            ),
            'fire_on_status_change' => false,
            'name' => '{{ product_instances:0:name }} - Move from {{ product_status:0:name }} to {{ product_status:1:name }}'
        ],
        1 => [
            'event_type_id' => 1, // 1 = Status Change
            'product_status_id' => '{{ product_status:1 }}', // Status to be in for this event to fire.
            'product_instance_id' => '{{ product_instance:0 }}',
            'options' => serialize(
                [
                    'status_id' => '{{ product_status:2 }}' // Status to move the order line into.
                ]
            ),
            'fire_on_status_change' => false,
            'name' => '{{ product_instances:0:name }} - Move from {{ product_status:1:name }} to {{ product_status:2:name }} on Supplier Allocated'
        ]
    ],
    'event_criteria_set' => [
        0 => [
            'event_id' => '{{ event_event:0 }}',
            'sort_order' => 0
        ],
        1 => [
            'event_id' => '{{ event_event:1 }}',
            'sort_order' => 0
        ]
    ],
    'event_criteria' => [
        0 => [
            'criteria_type_id' => 1, // Operator comparison.
            'criteria_set_id' => '{{ event_criteria_set:0 }}',
            'config' => serialize(
                [
                    'FOQuestion' => 'contact_for_inspection',
                    'FOValue' => '',
                    'FOUsePostSaveValue' => true,
                    'OperatorId' => 1, // 1 = Not Empty, 2 = Greater Than, 3 = Less Than
                    'SOUsePostSaveValue' => true
                ]
            )
        ],
        1 => [
            'criteria_type_id' => 4, // Order line user change.
            'criteria_set_id' => '{{ event_criteria_set:1 }}',
            'config' => serialize(
                [
                    'case_relationship_id' => 4
                ]
            )
        ],
        2 => [
            //event, criteria set and this criteria need configuring for a button.
            //a:1:{s:5:"value";s:15:"report_complete";}
        ]
    ],
    'scheduled events incl. rag'
    // Product Extras - Additional Fees
];
