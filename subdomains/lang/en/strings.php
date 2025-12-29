<?php

return [
    'no_domains' => 'No Domains',
    'domain' => 'Domain|Domains',

    'no_subdomains' => 'No Subdomains',
    'subdomain' => 'Subdomain|Subdomains',
    'limit' => 'Limit',
    'change_limit' => 'Change Limit',
    'limit_changed' => 'Limit changed',
    'limit_reached' => 'Subdomain Limit Reached',
    'create_subdomain' => 'Create Subdomain',

    'name' => 'Name',

    'srv_record' => 'SRV Record',
    'srv_record_help' => 'Enable this option to create a SRV record instead of an A or AAAA record.',

    'srv_target' => 'SRV Target',
    'srv_target_help' => 'The hostname that SRV records point to (for example: play.example.com).',

    'errors' => [
        'srv_target_missing' => 'Cannot enable SRV record because the selected domain does not have an SRV target set.',
    ],

    'api_token' => 'Cloudflare API Token',
    'api_token_help' => 'The token needs to have read permissions for Zone.Zone and write for Zone.Dns. For better security you can also set the "Zone Resources" to exclude certain domains and add the panel ip to the "Client IP Adress Filtering".',

    'notifications' => [
        'dns_created' => 'DNS record created on Cloudflare',
        'dns_updated' => 'DNS record updated on Cloudflare',
        'dns_deleted' => 'DNS record deleted from Cloudflare',
        'dns_action_failed' => 'Cloudflare DNS action failed',
        'zone_request_failed' => 'Cloudflare zone request failed',
        'zone_request_succeeded' => 'Cloudflare zone request succeeded',

        'cloudflare_missing_zone_title' => 'Cloudflare: Missing Zone ID',
        'cloudflare_missing_zone' => 'Cloudflare zone ID is not configured for :domain. Cannot upsert DNS record for :subdomain.',

        'cloudflare_missing_srv_port_title' => 'Cloudflare: Missing SRV Port',
        'cloudflare_missing_srv_port' => 'SRV target or port is missing for :subdomain. Cannot upsert SRV record.',

        'cloudflare_missing_srv_target_title' => 'Cloudflare: Missing SRV Target',
        'cloudflare_missing_srv_target' => 'SRV target is missing for :subdomain. Cannot upsert SRV record.',

        'cloudflare_record_updated_title' => 'Cloudflare: Record updated',
        'cloudflare_record_updated' => 'Successfully updated :subdomain to :record_type',

        'cloudflare_srv_upsert_failed_title' => 'Cloudflare: SRV upsert failed',
        'cloudflare_srv_upsert_failed' => 'Failed to upsert SRV record for :subdomain. See logs for details. Errors: :errors',

        'cloudflare_missing_ip_title' => 'Cloudflare: Missing IP',
        'cloudflare_missing_ip' => 'Server allocation IP is missing or invalid for :subdomain. Cannot upsert A/AAAA record.',

        'cloudflare_upsert_failed_title' => 'Cloudflare: Upsert failed',
        'cloudflare_upsert_failed' => 'Failed to upsert record for :subdomain. See logs for details. Errors: :errors',

        'cloudflare_delete_success_title' => 'Cloudflare: Record deleted',
        'cloudflare_delete_success' => 'Successfully deleted Cloudflare record for :subdomain.',

        'cloudflare_delete_failed_title' => 'Cloudflare: Delete failed',
        'cloudflare_delete_failed' => 'Failed to delete Cloudflare record for :subdomain. See logs for details. Errors: :errors',

        'cloudflare_zone_fetch_failed' => 'Failed to fetch Cloudflare Zone ID for domain: :domain',
        'cloudflare_domain_saved' => 'Successfully saved domain: :domain',
        'settings_saved' => 'Settings saved',
    ],
];
