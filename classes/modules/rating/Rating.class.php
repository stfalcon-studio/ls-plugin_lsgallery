<?php

class PluginLsgallery_ModuleRating extends PluginLsgallery_Inherit_ModuleRating
{

    /**
     * Vote for image
     * 
     * @param ModuleUser_EntityUser $oUser
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @param int $iValue
     * @return int
     */
    public function VoteImage(ModuleUser_EntityUser $oUser, PluginLsgallery_ModuleImage_EntityImage $oImage, $iValue)
    {
        $skill = $oUser->getSkill();

        $iDeltaRating = $iValue;
        if ($skill >= 100 and $skill < 250) {
            $iDeltaRating = $iValue * 2;
        } elseif ($skill >= 250 and $skill < 400) {
            $iDeltaRating = $iValue * 3;
        } elseif ($skill >= 400) {
            $iDeltaRating = $iValue * 4;
        }
        $oImage->setRating($oImage->getRating() + $iDeltaRating);
        /**
         * Начисляем силу и рейтинг автору топика, используя логарифмическое распределение
         */
        $iMinSize = 0.1;
        $iMaxSize = 8;
        $iSizeRange = $iMaxSize - $iMinSize;
        $iMinCount = log(0 + 1);
        $iMaxCount = log(500 + 1);
        $iCountRange = $iMaxCount - $iMinCount;
        if ($iCountRange == 0) {
            $iCountRange = 1;
        }
        if ($skill > 50 and $skill < 200) {
            $skill_new = $skill / 70;
        } elseif ($skill >= 200) {
            $skill_new = $skill / 10;
        } else {
            $skill_new = $skill / 100;
        }
        $iDelta = $iMinSize + (log($skill_new + 1) - $iMinCount) * ($iSizeRange / $iCountRange);
        /**
         * Сохраняем силу и рейтинг
         */
        $oUserImage = $this->User_GetUserById($oImage->getUserId());
        $iSkillNew = $oUserImage->getSkill() + $iValue * $iDelta;
        $iSkillNew = ($iSkillNew < 0) ? 0 : $iSkillNew;
        $oUserImage->setSkill($iSkillNew);
        $oUserImage->setRating($oUserImage->getRating() + $iValue * $iDelta / 2.73);
        $this->User_Update($oUserImage);
        return $iDeltaRating;
    }

}
