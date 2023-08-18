<?php
/**
 * Freeform for Craft CMS.
 *
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 *
 * @see           https://docs.solspace.com/craft/freeform
 *
 * @license       https://docs.solspace.com/license-agreement
 */

namespace Solspace\Freeform\Integrations\MailingLists\Campaign;

use craft\fields\Checkboxes;
use craft\fields\Color;
use craft\fields\Date;
use craft\fields\Dropdown;
use craft\fields\Email;
use craft\fields\Entries;
use craft\fields\Lightswitch;
use craft\fields\MultiSelect;
use craft\fields\Number;
use craft\fields\PlainText;
use craft\fields\RadioButtons;
use craft\fields\Tags;
use craft\fields\Url;
use craft\fields\Users;
use GuzzleHttp\Client;
use putyourlightson\campaign\elements\ContactElement;
use putyourlightson\campaign\elements\MailingListElement;
use Solspace\Freeform\Library\Integrations\DataObjects\FieldObject;
use Solspace\Freeform\Library\Integrations\Types\MailingLists\DataObjects\ListObject;
use Solspace\Freeform\Library\Integrations\Types\MailingLists\MailingListIntegration;

abstract class BaseCampaignIntegration extends MailingListIntegration implements CampaignIntegrationInterface
{
    protected const LOG_CATEGORY = 'Campaign';

    protected const CATEGORY_CUSTOM = 'Custom';

    public function checkConnection(Client $client): bool
    {
        return self::isInstallable();
    }

    public static function isInstallable(): bool
    {
        return \Craft::$app->plugins->isPluginInstalled('campaign');
    }

    public function fetchFields(ListObject $list, string $category, Client $client): array
    {
        $fieldTypeMap = [
            Checkboxes::class => FieldObject::TYPE_ARRAY,
            Color::class => FieldObject::TYPE_STRING,
            Date::class => FieldObject::TYPE_STRING,
            Dropdown::class => FieldObject::TYPE_STRING,
            Email::class => FieldObject::TYPE_STRING,
            Lightswitch::class => FieldObject::TYPE_BOOLEAN,
            Entries::class => FieldObject::TYPE_ARRAY,
            MultiSelect::class => FieldObject::TYPE_ARRAY,
            Number::class => FieldObject::TYPE_NUMERIC,
            PlainText::class => FieldObject::TYPE_STRING,
            RadioButtons::class => FieldObject::TYPE_STRING,
            Tags::class => FieldObject::TYPE_ARRAY,
            Url::class => FieldObject::TYPE_ARRAY,
            Users::class => FieldObject::TYPE_ARRAY,
        ];

        $fieldList = [];

        $fieldLayout = \Craft::$app->fields->getLayoutByType(ContactElement::class);

        foreach ($fieldLayout->getCustomFields() as $field) {
            $fieldClass = \get_class($field);

            if (!\in_array($fieldClass, array_keys($fieldTypeMap), true)) {
                continue;
            }

            $fieldList[] = new FieldObject(
                $field->handle,
                $field->name,
                $fieldTypeMap[$fieldClass],
                $category,
                $field->required,
            );
        }

        return $fieldList;
    }

    public function fetchLists(Client $client): array
    {
        $mailingLists = MailingListElement::find()
            ->site('*')
            ->orderBy([
                'elements_sites.slug' => 'ASC',
                'content.title' => 'ASC',
            ])
            ->all()
        ;

        $lists = [];

        foreach ($mailingLists as $list) {
            $lists[] = new ListObject(
                $list->id,
                $list->title.' ('.$list->site.')',
                $list->subscribedCount,
            );
        }

        return $lists;
    }
}
