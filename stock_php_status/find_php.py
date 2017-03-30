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
import sys
import logging
import openerp
import openerp.netsvc as netsvc
import openerp.addons.decimal_precision as dp
from openerp.osv import fields, osv, expression, orm
from datetime import datetime, timedelta
from dateutil.relativedelta import relativedelta
from openerp import SUPERUSER_ID, api
from openerp import tools
from openerp.tools.translate import _
from openerp.tools.float_utils import float_round as round
from openerp.tools import (DEFAULT_SERVER_DATE_FORMAT, 
    DEFAULT_SERVER_DATETIME_FORMAT, 
    DATETIME_FORMATS_MAP, 
    float_compare)


_logger = logging.getLogger(__name__)

class ProductProduct(orm.Model):
    """ Model name: ProductProduct
    """
    _inherit = 'product.product'

    def product_status_publish_php(self, cr, uid, context=None):
        ''' Override function for get prodcut selected for publish
        '''
        return self.search(cr, uid, [], context=context)
        
    def extract_web_php_inventory_file(self, cr, uid, context=None):
        ''' Extract and FTP publish status file
        '''        
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
        
        # Enable inventory status:
        _logger.info('Enable inventory status for read stock')
        user_pool = self.pool.get('res.users')
        user_pool.write(cr, uid, uid, {
            'no_inventory_status': False, 
            }, context=context)            
        
        # ---------------------------------------------------------------------
        #                        XLS log export:        
        # ---------------------------------------------------------------------
        company_proxy = self.pool.get('res.company').browse(
            cr, uid, [1], context=context)[0] # TODO change better
        filename = company_proxy.php_filename 
        if not filename:
            _logger.error('Set filename in company form!!')
            return True
            
        path = '/home/administrator/photo/output'
        publish = '/home/administrator/photo/output/publish.ftp.sh %s' % (
            filename, )
        fullname = os.path.join(path, filename)
        f_out = open(fullname, 'w')

        # ---------------------------------------------------------------------
        # Populate product in correct page
        # ---------------------------------------------------------------------
        selected_ids = self.product_status_publish_php(
            cr, uid, context=context)
        mask = '%s###FINERIGA###\n' % ('%s|' * 16) # generate mask
        for product in self.browse(cr, uid, selected_ids, context=context):
            # Only present text:
            mx_net_qty = product.mx_net_qty
            mx_lord_qty = product.mx_lord_qty
            if mx_net_qty <= 0 and mx_lord_qty <= 0:
                continue
                
            container = ''
            for transport in  product.transport_ids:
                if transport.quantity:
                    container += '%s ' % transport.quantity
                
            dazi = ''
            for tax in  product.duty_id.tax_ids:
                if tax.tax: # only present
                    dazi += '[%s] %s ' % (tax.country_id.code, tax.tax)
            try:
                mx_campaign_out = product.mx_campaign_out
            except:
                mx_campaign_out = 0.0 # no manage campaign
            f_out.write(mask % (        
                product.default_code, # 1. codice
                clean('%s %s' % (product.name, product.colour or '')), # 2. descrizione
                mx_net_qty, # 3. esistenza (no MRP)
                product.mx_oc_out, # 4. sospesi_cliente
                product.mx_of_in, # 5. ordinati
                mx_lord_qty, # 6. dispo_lorda (no MRP)
                product.lst_price, # 7. prezzo (calculated 50 + 20)
                product.mx_of_date, # 8. data_arrivo
                '', # 9. TODO status                
                mx_campaign_out, # 10. campagna
                product.standard_price, # 11. costo
                product.customer_cost, # 12. costo1  (fco/customer)
                product.company_cost, # 13. costo2  (fco/stock)
                dazi, # 14. dazi 
                container, # 15. container 
                product.inventory_category_id.id, # 16. inventory category
                ))
                
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
        'php_filename': fields.char('PHP filename', size=64),
        'php_category_ids': fields.many2many(
            'product.product.inventory.category', 'company_php_category_rel', 
            'company_id', 'category_id', 
            'Web product category', help='Inventory category published',
            ),
        }
# vim:expandtab:smartindent:tabstop=4:softtabstop=4:shiftwidth=4:
