import React, { useState } from 'react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm, Controller } from 'react-hook-form';
import { z } from 'zod';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Loader2 } from 'lucide-react';

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { useNavigate } from 'react-router-dom';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { toast } from 'sonner';

const formSchema = z.object({
  pooja_date: z.string().min(1, 'pooja date filed is required.'),
  pooja_type_id: z.coerce.number().min(1, 'pooja type field is required'),
});
const Create = () => {
  const [isLoading, setIsLoading] = useState(false);
  const queryClient = useQueryClient();
  const user = JSON.parse(localStorage.getItem('user'));
  const token = user.token;
  const navigate = useNavigate();
  const defaultValues = {
    pooja_type_id: '',
    pooja_date: '',
  };

  const {
    control,
    handleSubmit,
    formState: { errors },
    setError,
  } = useForm({ resolver: zodResolver(formSchema), defaultValues });

  const {
    data: allPoojaTypesData,
    isLoading: isAllPoojaTypeDataLoading,
    isError: isAllPoojaTypeDataError,
  } = useQuery({
    queryKey: ['allPoojaType'], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(
          `http://127.0.0.1:8000/api/all_pooja_types`,
          {
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${token}`,
            },
          }
        );
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  const storeMutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.post('/api/pooja_dates', data, {
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`, // Include the Bearer token
        },
      });
      return response.data;
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries('poojaDates');
      toast.success('Pooja Date Added Successfully');
      setIsLoading(false);
      navigate('/pooja_dates');
    },
    onError: (error) => {
      setIsLoading(false);
      if (error.response && error.response.data.errors) {
        const serverStatus = error.response.data.status;
        const serverErrors = error.response.data.errors;
        if (serverStatus === false) {
          toast.error('Failed to add pooja Date.');
        } else {
          toast.error('Failed to add pooja Date.');
        }
      } else {
        toast.error('Failed to add pooja Date.');
      }
      console.log('got error ', error);
    },
  });
  const onSubmit = (data) => {
    setIsLoading(true);
    storeMutation.mutate(data);
  };

  return (
    <>
      <div className="p-5">
        {/* breadcrumb start */}
        <div className=" mb-7 text-sm">
          <div className="flex items-center space-x-2 text-gray-700">
            <span className="">
              <Button
                onClick={() => navigate('/pooja_dates')}
                className="p-0 text-blue-700 text-sm font-light"
                variant="link"
              >
                Pooja Dates
              </Button>
            </span>
            <span className="text-gray-400">/</span>
            <span className="dark:text-gray-300">Add</span>
          </div>
        </div>
        {/* breadcrumb ends */}

        {/* form style strat */}
        <div className="px-5 pb-7 dark:bg-background pt-1 w-full bg-white shadow-lg border  rounded-md">
          <div className="w-full py-3 flex justify-start items-center">
            <h2 className="text-lg  font-normal">Add Pooja Date</h2>
          </div>
          {/* row starts */}
          <form onSubmit={handleSubmit(onSubmit)}>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-2 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="pooja_type_id">
                  Pooja Type: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="pooja_type_id"
                  control={control}
                  render={({ field }) => (
                    <Select value={field.value} onValueChange={field.onChange}>
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select Pooja Type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectLabel>Select Pooja Type</SelectLabel>
                          {allPoojaTypesData?.PoojaTypes &&
                            allPoojaTypesData?.PoojaTypes.map((poojaType) => (
                              <SelectItem value={String(poojaType.id)}>
                                {poojaType.pooja_type}
                              </SelectItem>
                            ))}
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  )}
                />
                {errors.pooja_type_id && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.pooja_type_id.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="pooja_date">
                  Pooja date:<span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="pooja_date"
                  control={control}
                  render={({ field }) => (
                    <input
                      {...field}
                      id="pooja_date"
                      className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                      type="date"
                      placeholder="Enter pooja date"
                    />
                  )}
                />
                {errors.pooja_date && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.pooja_date.message}
                  </p>
                )}
              </div>
            </div>
            {/* row ends */}
            <div className="w-full gap-4 mt-4 flex justify-end items-center">
              <Button
                type="button"
                className="dark:text-white shadow-xl bg-red-600 hover:bg-red-700"
                onClick={() => navigate('/pooja_dates')}
              >
                Cancel
              </Button>

              <Button
                type="submit"
                disabled={isLoading}
                className=" dark:text-white  shadow-xl bg-green-600 hover:bg-green-700"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="animate-spin mr-2" /> {/* Spinner */}
                    Submitting...
                  </>
                ) : (
                  'Submit'
                )}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </>
  );
};

export default Create;
