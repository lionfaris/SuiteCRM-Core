<?php

namespace App\Legacy\Statistics;

use App\Entity\Statistic;
use App\Service\StatisticsProviderInterface;
use App\Legacy\Data\PresetDataHandlers\SubpanelDataQueryHandler;

/**
 * Class SubPanelActivitiesNextDate
 * @package App\Legacy\Statistics
 */
class SubPanelContactsCount extends SubpanelDataQueryHandler  implements StatisticsProviderInterface
{
    use StatisticsHandlingTrait;

    public const KEY = 'contacts';

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return self::KEY;
    }

    /**
     * @inheritDoc
     */
    public function getData(array $query): Statistic
    {
        $subpanel = 'contacts';
        [$module, $id] = $this->extractContext($query);
        if (empty($module) || empty($id)) {
            return $this->getEmptyResponse(self::KEY);
        }

        $this->init();
        $this->startLegacyApp();

        $queries = $this->getQueries($module, $id, $subpanel);
        $parts = $queries[0];
        $parts['select'] = 'SELECT COUNT(*) as value';

        $dbQuery = $this->joinQueryParts($parts);
        $result = $this->fetchRow($dbQuery);
        $statistic = $this->buildSingleValueResponse(self::KEY, 'int', $result);

        $this->close();

        return $statistic;
    }
}
