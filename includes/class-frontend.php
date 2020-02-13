<?php
/**
 * Outputs the front-end interface.
 *
 * @package pmpro-emailoctopus
 */

namespace PMProEmailOctopus\Includes;

use PMProEmailOctopus\Includes\Options as Options;

/**
 * Class Frontend
 */
class Frontend {

	/**
	 * Class initializer.
	 */
	public function run() {
		add_action( 'pmpro_checkout_after_tos_fields', array( $this, 'output_lists' ) );
		add_action( 'pmpro_after_checkout', array( $this, 'after_checkout' ), 15 );
	}

	/**
	 * Output lists on the checkout page.
	 */
	public function output_lists() {
		$admin        = new \PMProEmailOctopus\Includes\Admin();
		$lists        = $admin->get_lists();
		$options      = Options::get_options();
		$list_to_show = array();
		if ( ! $lists || empty( $lists ) ) {
			return;
		}
		foreach ( $lists['data'] as $list ) {
			if ( in_array( $list['id'], $options['user_lists'], true ) ) {
				$lists_to_show[] = array(
					'list' => $list['id'],
					'name' => $list['name'],
				);
			}
		}
		if ( empty( $lists_to_show ) ) {
			return;
		}
		?>
		<table id="pmpro_mailing_lists_emailoctopus" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0"
			border="0">
		<thead>
		<tr>
			<th>
				<?php
				if ( count( $lists_to_show ) > 1 ) {
					esc_html_e( 'Join one or more of our mailing lists.', 'pmpro-emailoctopus' );
				} else {
					esc_html_e( 'Join our mailing list.', 'pmpro-emailoctopus' );
				}
				?>
			</th>
		</tr>
		</thead>
		<tbody>
		<tr class="odd">
			<td>
				<?php
				$count = 0;
				foreach ( $lists_to_show as $key => $list_to_show ) {
					$count++;
					?>
					<input type="checkbox" id="emailoctopus_lists_<?php echo absint( $count ); ?>" name="emailoctopus_lists[]" value="<?php echo esc_attr( $list_to_show['list'] ); ?>"  />&nbsp;&nbsp;
					<label style="display: inline-block" for="emailoctopus_lists_<?php echo absint( $count ); ?>"
							class="pmpro_normal pmpro_clickable"><?php echo esc_html( $list_to_show['name'] ); ?></label><br/>
					<?php
				}
				?>
			</td>
		</tr>
		</tbody>
	</table>
		<?php
	}

	/**
	 * Runs after checkout and subscribes the user to a list.
	 *
	 * @param int $user_id The user id.
	 */
	public function after_checkout( $user_id ) {
		$user          = get_user_by( 'id', $user_id );
		$email_address = $user->user_email;
		if ( is_email( $email_address ) ) {
			$api_helper = new \PMProEmailOctopus\Includes\API();
			$lists      = filter_input( INPUT_POST, 'emailoctopus_lists', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( $lists ) {
				$api_helper->subscribe( $email_address, $lists );
			}
		}
	}
}
