# -*- coding: utf-8 -*-
###############################################################################
#
#    Copyright (C) 2001-2014 Micronaet SRL (<http://www.micronaet.it>).
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as published
#    by the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
###############################################################################
import os
import logging
import pdb

from openerp.osv import fields, osv, expression, orm
from datetime import datetime, timedelta
from openerp import SUPERUSER_ID, api
from openerp import tools
from openerp.tools.translate import _
from openerp.tools import (DEFAULT_SERVER_DATE_FORMAT,
    DEFAULT_SERVER_DATETIME_FORMAT,
    DATETIME_FORMATS_MAP,
    float_compare)

_logger = logging.getLogger(__name__)


class ProductProduct(orm.Model):
    """ Model name: ProductProduct
    """
    _inherit = 'product.product'

    def check_excel_export(self, product):
        """ Override function for get product select for excel
        """
        return True

    def product_status_publish_php(self, cr, uid, context=None):
        """ Override function for get product selected for publish
        """
        return self.search(cr, uid, [], context=context)

    def extract_web_php_inventory_file(self, cr, uid, context=None):
        """ Extract and FTP publish status file
        """
        def clean(value):
            res = ''
            for c in value:
                if ord(c) < 127:
                    res += c
                else:
                    res += '#'
            return res

        _logger.warning('Start export PHP')
        if context is None:
            context = {}
        context['lang'] = 'it_IT'

        # Pool used:
        excel_pool = self.pool.get('excel.writer')
        user_pool = self.pool.get('res.users')

        # Enable inventory status:
        _logger.info('Enable inventory status for read stock')
        user_pool.write(cr, uid, uid, {
            'no_inventory_status': False,
            }, context=context)

        # ---------------------------------------------------------------------
        #                        XLS log export:
        # ---------------------------------------------------------------------
        company_proxy = self.pool.get('res.company').browse(
            cr, uid, [1], context=context)[0]  # todo change better
        filename = company_proxy.php_filename
        php_no_order = company_proxy.php_no_order

        if not filename:
            _logger.error('Set filename in company form!!')
            return True

        path = '/home/administrator/photo/output'
        excel_filename = '%s.xlsx' % cr.dbname
        excel_fullname = os.path.join(path, excel_filename)

        publish = '%s/publish.excel.ftp.sh %s %s' % (
            path, filename, excel_filename)
        fullname = os.path.join(path, filename)
        f_out = open(fullname, 'w')

        # ---------------------------------------------------------------------
        # Excel file:
        # ---------------------------------------------------------------------
        ws_name = 'Stato Magazzino'
        excel_pool.create_worksheet(ws_name)
        excel_pool.set_format()
        excel_format = {
            'title': excel_pool.get_format('title'),
            'header': excel_pool.get_format('header'),
            'text': {
                'white': excel_pool.get_format('text'),
                'red': excel_pool.get_format('bg_red'),
                'green': excel_pool.get_format('bg_green'),
                },
            'number': {
                'white': excel_pool.get_format('number'),
                'red': excel_pool.get_format('bg_red_number'),
                'green': excel_pool.get_format('bg_green_number'),
                },
            }

        # ---------------------------------------------------------------------
        # Write header:
        # ---------------------------------------------------------------------
        excel_pool.column_width(ws_name, (
            5, 15, 40, 10, 15, 20, 10, 15,
            ))

        row = 0
        excel_pool.write_xls_line(ws_name, row, (
            'Stato magazzino: %s' % datetime.now(),
            ), default_format=excel_format['title'])

        row += 1
        excel_pool.write_xls_line(ws_name, row, (
            'Immagine', 'Codice', 'Descrizione', 'Disponibili',
            'Ordini fornitori', 'Date arrivo', 'Campagne', 'Prezzo listino',
            ), default_format=excel_format['header'])

        # ---------------------------------------------------------------------
        # Populate product in correct page
        # ---------------------------------------------------------------------
        selected_ids = self.product_status_publish_php(
            cr, uid, context=context)
        mask = '%s###FINERIGA###\n' % ('%s|' * 16)  # generate mask
        for product in self.browse(cr, uid, selected_ids, context=context):
            # Only present text:
            mx_mrp_out = product.mx_mrp_out  # out for production
            if product.default_code == '045XW NENE  S':
                pdb.set_trace()

            if php_no_order:  # MRP Mode:
                # No order only, consider only: MRP locked or Stock locked:
                mx_net_qty = product.mx_net_mrp_qty
                mx_lord_qty = mx_net_qty - product.mx_mrp_b_locked
            else:
                # Consider net without order:
                mx_net_qty = product.mx_net_qty - mx_mrp_out
                mx_lord_qty = product.mx_lord_qty - mx_mrp_out
            if mx_net_qty <= 0 and mx_lord_qty <= 0:
                continue

            container = ''
            for transport in product.transport_ids:
                if transport.quantity:
                    container += '%s ' % transport.quantity

            dazi = ''
            for tax in product.duty_id.tax_ids:
                if tax.tax:  # only present
                    dazi += '[%s] %s ' % (tax.country_id.code, tax.tax)
            try:
                mx_campaign_out = product.mx_campaign_out
            except:
                mx_campaign_out = 0.0  # no manage campaign
            name = clean('%s %s' % (product.name, product.colour or ''))
            f_out.write(mask % (
                product.default_code,  # 1. codice
                name,  # 2. descrizione
                mx_net_qty,  # 3. esistenza (no MRP)
                product.mx_oc_out,  # 4. sospesi_cliente
                product.mx_of_in,  # 5. ordinati
                mx_lord_qty,  # 6. dispo_lorda (no MRP)
                product.lst_price,  # 7. prezzo (calculated 50 + 20)
                product.mx_of_date,  # 8. data_arrivo
                '',  # 9. todo status
                mx_campaign_out,  # 10. campagna
                product.standard_price,  # 11. costo
                product.customer_cost,  # 12. costo1  (fco/customer)
                product.company_cost,  # 13. costo2  (fco/stock)
                dazi,  # 14. dazi
                container,  # 15. container
                product.inventory_category_id.id,  # 16. inventory category
                ))

            # -----------------------------------------------------------------
            # Excel export:
            # -----------------------------------------------------------------
            if not self.check_excel_export(product):
                continue  # Not exported

            availability = mx_net_qty - product.mx_oc_out

            # Color setup:
            if availability > 0:
                text_color = excel_format['text']['green']
                number_color = excel_format['number']['green']
            elif availability < 0:
                text_color = excel_format['text']['red']
                number_color = excel_format['number']['red']
            else:
                text_color = excel_format['text']['white']
                number_color = excel_format['number']['white']

            row += 1
            excel_pool.write_xls_line(ws_name, row, (
                '',
                product.default_code,  # 1. codice
                name,  # 2. descrizione
                (availability, number_color),
                (product.mx_of_in, number_color),  # 5. ordinati
                product.mx_of_date,  # 8. data_arrivo
                (mx_campaign_out, number_color),  # 10. campagna
                (product.lst_price, number_color),  # 7. prezzo (50 + 20)
                # product.standard_price,  # 11. costo
                # product.customer_cost,  # 12. costo1  (fco/customer)
                # product.company_cost,  # 13. costo2  (fco/stock)
                ), default_format=text_color)

        # Excel save file:
        excel_pool.save_file_as(excel_fullname)

        # Publish via FTP and call import document
        _logger.warning('Run %s' % publish)
        os.system(publish)
        _logger.warning('End export PHP')
        return True


class ResCompany(orm.Model):
    """ Model name: ResCompany
    """
    _inherit = 'res.company'

    _columns = {
        'php_no_order': fields.boolean(
            'PHP No ordini',
            help='Nelle ditte di produzione la esistenza disponibile '
                 'viene indicata senza gli ordini dato che, quasi sempre,'
                 'gli ordini cliente vengono prodotti a tempo debito e '
                 'gli ordini piccoli hanno delle assegnazioni di merce.'
        ),
        'php_filename': fields.char('PHP filename', size=64),
        'php_category_ids': fields.many2many(
            'product.product.inventory.category', 'company_php_category_rel',
            'company_id', 'category_id',
            'Web product category', help='Inventory category published',
            ),
        }
